<?php

namespace App\Http\Controllers;

use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        return PromotionResource::collection(
            Promotion::query()->orderByDesc('created_at')->paginate(20)
        );
    }

    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        return (new PromotionResource($promotion))->response()->setStatusCode(201);
    }

    public function show(Promotion $promotion)
    {
        return new PromotionResource($promotion);
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $promotion->update($request->validated());

        return new PromotionResource($promotion);
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->noContent();
    }
}
