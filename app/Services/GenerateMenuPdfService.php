<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class GenerateMenuPdfService
{
    public function buildData(Menu $menu): array
    {
        $menu->load([
            'categories',
            'products.variants.images',
        ]);

        $menuProducts = $menu->products->keyBy('id');

        $categories = $menu->categories
            ->map(function ($category) use ($menuProducts) {

                $products = $menuProducts
                    ->filter(function ($product) use ($category) {
                        return $product->category_id === $category->id && $product->is_active;
                    })
                    ->map(function ($product) {
                        $variants = $product->variants
                            ->where('is_active', true)
                            ->sortBy('position')
                            ->map(fn($v) => [
                                'label'  => $v->label,
                                'price'  => $v->price,
                                'images' => $v->images
                                    ->sortBy('position')
                                    ->map(fn($image) => [
                                        'id'       => $image->id,
                                        'url'      => $image->image_url,
                                        'position' => $image->position,
                                        'pdf_src'  => $this->resolvePdfImageSrc($image->image_url),
                                    ])
                                    ->values()
                                    ->all(),
                            ])
                            ->values()
                            ->all();

                        return [
                            'id'          => $product->id,
                            'name'        => $product->name,
                            'description' => $product->description,
                            'position'    => $product->pivot->position,
                            'variants'    => $variants,
                        ];
                    })
                    ->sortBy('position')
                    ->values()
                    ->all();

                if (empty($products)) {
                    return null;
                }

                return [
                    'id'            => $category->id,
                    'name'          => $category->name,
                    'description'   => $category->description ?? null,
                    'position'      => $category->pivot->position ?? null,
                    'price_display' => $category->price_display ?? 'auto',
                    'is_full_width' => (bool) ($category->is_full_width ?? false),
                    'products'      => $products,
                ];
            })
            ->filter()
            ->sortBy('position')
            ->values()
            ->all();

        return [
            'id'          => $menu->id,
            'name'        => $menu->name,
            'description' => $menu->description,
            'is_active'   => $menu->is_active,
            'categories'  => $categories,
            'assets'      => [
                'logo'    => $this->resolvePublicAsset('logo-little-claire.png'),
                'qr_code' => $this->resolvePublicAsset('qr-code.png'),
                'divider' => $this->resolvePublicAsset('divider-line.svg'),
            ],
        ];
    }

    public function handle(Menu $menu): array
    {
        $data = $this->buildData($menu);

        $html = View::make('pdf.menu', [
            'data' => $data,
        ])->render();

        $directory = storage_path('app/public/menus');
        $absolutePath = $directory . "/menu-{$menu->id}.pdf";

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->disableJavascript()
            ->timeout(120);

        $nodePath = config('services.browsershot.node');
        $npmPath = config('services.browsershot.npm');
        $chromePath = config('services.browsershot.chrome');

        if (is_string($nodePath) && $nodePath !== '') {
            $browsershot->setNodeBinary($nodePath);
        }

        if (is_string($npmPath) && $npmPath !== '') {
            $browsershot->setNpmBinary($npmPath);
        }

        if (is_string($chromePath) && $chromePath !== '') {
            $browsershot->setChromePath($chromePath);
        }

        $browsershot->savePdf($absolutePath);

        return [
            'relative_path' => "storage/menus/menu-{$menu->id}.pdf",
            'absolute_path' => $absolutePath,
            'public_url' => asset("storage/menus/menu-{$menu->id}.pdf"),
            'exists' => File::exists($absolutePath),
            'size' => File::exists($absolutePath) ? File::size($absolutePath) : 0,
        ];
    }

    private function resolvePublicAsset(string $filename): ?string
    {
        $path = public_path('pdf-assets/' . $filename);

        if (!File::exists($path)) {
            return null;
        }

        $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'svg'  => 'image/svg+xml',
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };

        $contents = File::get($path);

        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $contents = $this->resizeImage($contents, 440);
            $mime = 'image/png';
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    private function resolvePdfImageSrc(?string $imageUrl): ?string
    {
        if (!$imageUrl) {
            return null;
        }

        // Caso 1: URL absoluta remota (https://...)
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::timeout(10)->get($imageUrl);

                if ($response->successful()) {
                    $mime = $response->header('Content-Type') ?: 'image/jpeg';
                    $body = $response->body();

                    if (!empty($body)) {
                        $body = $this->resizeImage($body, 128);
                        return 'data:image/png;base64,' . base64_encode($body);
                    }
                }
            } catch (\Throwable $e) {
                return $imageUrl;
            }

            return $imageUrl;
        }

        // Caso 2: image_url guarda algo como "storage/products/foo.jpg"
        $normalizedPath = str_replace('\\', '/', $imageUrl);
        $normalizedPath = ltrim($normalizedPath, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            $absolutePath = Storage::disk('public')->path($normalizedPath);

            if (File::exists($absolutePath)) {
                $contents = $this->resizeImage(File::get($absolutePath), 128);
                return 'data:image/png;base64,' . base64_encode($contents);
            }
        }

        // Caso 3: path relativo dentro de public/ (e.g. "/product-images/foo.png")
        $publicPath = public_path($normalizedPath);
        if (File::exists($publicPath)) {
            $contents = $this->resizeImage(File::get($publicPath), 128);
            return 'data:image/png;base64,' . base64_encode($contents);
        }

        return $imageUrl;
    }

    private function resizeImage(string $binaryData, int $maxPx): string
    {
        if (!extension_loaded('gd')) return $binaryData;

        $src = @imagecreatefromstring($binaryData);
        if (!$src) return $binaryData;

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= $maxPx) {
            imagedestroy($src);
            return $binaryData;
        }

        $newH = (int) round($h * $maxPx / $w);
        $dst  = imagecreatetruecolor($maxPx, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxPx, $newH, $w, $h);
        imagedestroy($src);

        ob_start();
        imagepng($dst, null, 9);
        $out = ob_get_clean();
        imagedestroy($dst);

        return $out ?: $binaryData;
    }
}
