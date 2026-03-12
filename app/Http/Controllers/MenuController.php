<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Menu::all(['id', 'name', 'description', 'is_active']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->validated());

        return response()->json($menu, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return $menu->only(['id', 'name', 'description', 'is_active']);
    }

    public function showProducts(int $id)
    {
        $data = $this->getMenuProducts($id);
        return view('pdf', ['data' => $data]);
    }


    /**
     * Display the specified resource.
     */
    private function getMenuProducts(int $id)
    {
        $menu = Menu::with([
            'categories.products.images',
            'products',
        ])->findOrFail($id);

        $menuProducts = $menu->products->keyBy('id');

        $categories = $menu->categories->map(function ($category) use ($menuProducts) {
            $products = $category->products
                ->filter(function ($product) use ($menuProducts) {
                    return $menuProducts->has($product->id);
                })
                ->map(function ($product) use ($menuProducts) {
                    $menuProduct = $menuProducts->get($product->id);

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'custom_price' => $menuProduct->pivot->custom_price,
                        'final_price' => $menuProduct->pivot->custom_price ?? $product->price,
                        'position' => $menuProduct->pivot->position,
                        'is_active' => $product->is_active,
                        'images' => $product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => $image->image_url ?? null,
                                'position' => $image->position,
                            ];
                        })->values(),
                    ];
                })
                ->sortBy('position')
                ->values();

            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description ?? null,
                'position' => $category->pivot->position ?? null,
                'products' => $products,
            ];
        })->sortBy('position')->values();
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'description' => $menu->description,
            'is_active' => $menu->is_active,
            'categories' => $categories,
        ];
        // return response()->json([
        //     'id' => $menu->id,
        //     'name' => $menu->name,
        //     'description' => $menu->description,
        //     'is_active' => $menu->is_active,
        //     'categories' => $categories,
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());
        return response()->json($menu, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->noContent(204);
    }
}
