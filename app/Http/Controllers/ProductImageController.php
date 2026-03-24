<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImage\StoreProductImageRequest;
use App\Http\Requests\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\GenerateMenuPdfService;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function index(Product $product, ProductVariant $variant)
    {
        return ProductImageResource::collection($variant->images);
    }

    public function store(StoreProductImageRequest $request, Product $product, ProductVariant $variant, GenerateMenuPdfService $pdfService)
    {
        $path = $request->file('image')->store('products', 'public');

        $image = $variant->images()->create([
            'image_url' => 'storage/' . $path,
            'position'  => $request->input('position'),
        ]);

        foreach ($product->menus as $menu) {
            $pdfService->handle($menu);
        }

        return (new ProductImageResource($image))->response()->setStatusCode(201);
    }

    public function update(UpdateProductImageRequest $request, Product $product, ProductVariant $variant, ProductImage $image)
    {
        $image->update($request->validated());

        return new ProductImageResource($image);
    }

    public function destroy(Product $product, ProductVariant $variant, ProductImage $image, GenerateMenuPdfService $pdfService)
    {
        if (str_starts_with($image->image_url, 'storage/')) {
            Storage::disk('public')->delete(substr($image->image_url, strlen('storage/')));
        }

        $image->delete();

        foreach ($product->menus as $menu) {
            $pdfService->handle($menu);
        }

        return response()->noContent();
    }
}
