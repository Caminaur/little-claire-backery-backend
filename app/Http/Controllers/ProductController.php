<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::query()
            ->with('variants')
            ->orderBy('id')
            ->get([
                'id',
                'category_id',
                'name',
                'description',
                'is_active',
            ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $product->load('variants');

        return $product->only([
            'id',
            'category_id',
            'name',
            'description',
            'is_active',
            'variants',
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json($product, 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
