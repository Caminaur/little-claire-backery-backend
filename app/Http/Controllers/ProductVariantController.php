<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\GenerateMenuPdfService;
use Illuminate\Support\Facades\Log;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        return ProductVariantResource::collection($product->variants);
    }

    public function store(StoreProductVariantRequest $request, Product $product, GenerateMenuPdfService $pdfService)
    {
        $variant = $product->variants()->create($request->validated());

        foreach ($product->menus as $menu) {
            $pdfService->handle($menu);
        }

        return (new ProductVariantResource($variant))->response()->setStatusCode(201);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        return new ProductVariantResource($variant);
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant, GenerateMenuPdfService $pdfService)
    {
        $variant->update($request->validated());

        $menus = $product->menus;
        Log::info('ProductVariantController@update PDF regen', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'menu_count' => $menus->count(),
            'menu_ids'   => $menus->pluck('id'),
        ]);

        foreach ($menus as $menu) {
            try {
                $pdfService->handle($menu);
                Log::info('PDF regenerated for menu', ['menu_id' => $menu->id]);
            } catch (\Throwable $e) {
                Log::error('PDF regen failed', ['menu_id' => $menu->id, 'error' => $e->getMessage()]);
            }
        }

        return new ProductVariantResource($variant);
    }

    public function destroy(Product $product, ProductVariant $variant, GenerateMenuPdfService $pdfService)
    {
        $variant->delete();

        foreach ($product->menus as $menu) {
            $pdfService->handle($menu);
        }

        return response()->noContent();
    }
}
