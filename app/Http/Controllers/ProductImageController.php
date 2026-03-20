<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImage\StoreProductImageRequest;
use App\Http\Requests\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

class ProductImageController extends Controller
{
    public function index(Product $product, ProductVariant $variant)
    {
        return ProductImageResource::collection($variant->images);
    }

    public function store(StoreProductImageRequest $request, Product $product, ProductVariant $variant)
    {
        $image = $variant->images()->create($request->validated());

        return (new ProductImageResource($image))->response()->setStatusCode(201);
    }

    public function update(UpdateProductImageRequest $request, Product $product, ProductVariant $variant, ProductImage $image)
    {
        $image->update($request->validated());

        return new ProductImageResource($image);
    }

    public function destroy(Product $product, ProductVariant $variant, ProductImage $image)
    {
        $image->delete();

        return response()->noContent();
    }
}
