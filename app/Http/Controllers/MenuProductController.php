<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuProduct\ReorderMenuProductRequest;
use App\Http\Requests\MenuProduct\StoreMenuProductRequest;
use App\Http\Requests\MenuProduct\UpdateMenuProductRequest;
use App\Http\Resources\MenuProductResource;
use App\Models\Menu;
use App\Models\MenuProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuProductController extends Controller
{
    /**
     * List all products configured for the given menu,
     * including pivot position and custom price.
     */
    public function index(Menu $menu)
    {
        return MenuProductResource::collection($menu->products);
    }

    /**
     * Attach an existing product to the given menu.
     */
    public function store(StoreMenuProductRequest $request, Menu $menu)
    {
        $data = $request->validated();

        $exists = MenuProduct::where('menu_id', $menu->id)
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Product already attached to this menu.'
            ], 409);
        }

        $menuProduct = MenuProduct::create([
            'menu_id' => $menu->id,
            'product_id' => $data['product_id'],
            'position' => $data['position'],
            'custom_price' => $data['custom_price'],
        ]);

        return response()->json($menuProduct, 201);
    }

    /**
     * Reorder products inside a menu.
     */
    public function reorder(ReorderMenuProductRequest $request, Menu $menu)
    {
        $data = $request->validated();

        foreach ($data['products'] as $item) {
            MenuProduct::where('menu_id', $menu->id)
                ->where('product_id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->noContent();
    }

    /**
     * Update custom price of a product inside a menu.
     */
    public function update(updateMenuProductRequest $request, Menu $menu, Product $product)
    {
        $data = $request->validated();

        MenuProduct::where('menu_id', $menu->id)
            ->where('product_id', $product->id)
            ->update(['custom_price' => $data['custom_price']]);

        return response()->noContent();
    }

    /**
     * Detach a product from the given menu.
     */
    public function destroy(Menu $menu, Product $product)
    {
        MenuProduct::where('menu_id', $menu->id)
            ->where('product_id', $product->id)
            ->delete();

        return response()->noContent();
    }
}
