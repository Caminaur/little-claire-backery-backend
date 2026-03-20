<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        return ProductVariantResource::collection($product->variants);
    }

    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $variant = $product->variants()->create($request->validated());

        return (new ProductVariantResource($variant))->response()->setStatusCode(201);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        return new ProductVariantResource($variant);
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant)
    {
        $variant->update($request->validated());

        return new ProductVariantResource($variant);
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();

        return response()->noContent();
    }
}
