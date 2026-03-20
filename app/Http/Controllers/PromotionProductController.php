<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionProduct\StorePromotionProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Promotion;

class PromotionProductController extends Controller
{
    public function index(Promotion $promotion)
    {
        return ProductResource::collection($promotion->products);
    }

    public function store(StorePromotionProductRequest $request, Promotion $promotion)
    {
        $productId = $request->validated()['product_id'];

        $exists = $promotion->products()->where('product_id', $productId)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Product already attached to this promotion.'
            ], 409);
        }

        $promotion->products()->attach($productId);

        return response()->json(['product_id' => $productId], 201);
    }

    public function destroy(Promotion $promotion, Product $product)
    {
        $promotion->products()->detach($product->id);

        return response()->noContent();
    }
}
