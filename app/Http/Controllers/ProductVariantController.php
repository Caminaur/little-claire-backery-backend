<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        return $product->variants;
    }

    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $variant = $product->variants()->create($request->validated());

        return response()->json($variant, 201);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        return $variant;
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant)
    {
        $variant->update($request->validated());

        return response()->json($variant, 200);
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();

        return response()->noContent();
    }
}
