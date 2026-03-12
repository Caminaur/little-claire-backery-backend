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
    public function handle(Menu $menu): array
    {
        $menu->load([
            'categories.products.images',
            'products',
        ]);

        $menuProducts = $menu->products->keyBy('id');

        $categories = $menu->categories
            ->map(function ($category) use ($menuProducts) {

                $products = $menuProducts
                    ->filter(function ($product) use ($category) {
                        return $product->category_id === $category->id && $product->is_active;
                    })
                    ->map(function ($product) {

                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => $product->price,
                            'custom_price' => $product->pivot->custom_price,
                            'final_price' => $product->pivot->custom_price ?? $product->price,
                            'position' => $product->pivot->position,
                            'images' => $product->images
                                ->sortBy('position')
                                ->map(function ($image) {
                                    return [
                                        'id' => $image->id,
                                        'url' => $image->image_url,
                                        'position' => $image->position,
                                        'pdf_src' => $this->resolvePdfImageSrc($image->image_url),
                                    ];
                                })
                                ->values()
                                ->all(),
                        ];
                    })
                    ->sortBy('position')
                    ->values()
                    ->all();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description ?? null,
                    'position' => $category->pivot->position ?? null,
                    'products' => $products,
                ];
            })
            ->sortBy('position')
            ->values()
            ->all();

        $data = [
            'id' => $menu->id,
            'name' => $menu->name,
            'description' => $menu->description,
            'is_active' => $menu->is_active,
            'categories' => $categories,
        ];

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
            ->showBackground();

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
                        return 'data:' . $mime . ';base64,' . base64_encode($body);
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
                $mime = File::mimeType($absolutePath) ?: 'image/jpeg';
                $contents = File::get($absolutePath);

                return 'data:' . $mime . ';base64,' . base64_encode($contents);
            }
        }

        return $imageUrl;
    }
}
