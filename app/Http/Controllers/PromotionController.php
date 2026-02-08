<?php

namespace App\Http\Controllers;

use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        return Promotion::query()
            ->orderByDesc('created_at')
            ->get([
                'id',
                'title',
                'description',
                'discount_type',
                'discount_value',
                'starts_at',
                'ends_at',
                'is_active',
            ]);
    }

    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        return response()->json($promotion, 201);
    }

    public function show(Promotion $promotion)
    {
        return $promotion;
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $data = $request->validated();

        $promotion->update($data);

        return response()->json($promotion, 200);
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->noContent();
    }
}
