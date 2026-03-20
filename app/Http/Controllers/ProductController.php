<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::query()->with('variants')->orderBy('id')->paginate(20)
        );
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show(Product $product)
    {
        $product->load('variants');

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
