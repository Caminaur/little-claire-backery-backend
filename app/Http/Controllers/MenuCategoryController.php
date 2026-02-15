<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategoryResource;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{

    /**
     * List all categories configured for the given menu,
     * including their pivot position, ordered by position.
     */

    public function index(Menu $menu)
    {
        return MenuCategoryResource::collection($menu->categories);
    }

    /**
     * Attach an existing category to the given menu.
     */
    public function store(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'position' => 'required|integer|min:1',
        ]);

        // Prevent duplicates
        $exists = MenuCategory::where('menu_id', $menu->id)
            ->where('category_id', $data['category_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Category already attached to this menu.'
            ], 409);
        }

        $menuCategory = MenuCategory::create([
            'menu_id' => $menu->id,
            'category_id' => $data['category_id'],
            'position' => $data['position'],
        ]);

        return response()->json($menuCategory, 201);
    }

    /**
     * Reorder categories inside a menu.
     */
    public function reorder(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer|min:1',
        ]);

        foreach ($data['categories'] as $item) {
            MenuCategory::where('menu_id', $menu->id)
                ->where('category_id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->noContent();
    }

    /**
     * Detach a category from the given menu.
     */
    public function destroy(Menu $menu, Category $category)
    {
        MenuCategory::where('menu_id', $menu->id)
            ->where('category_id', $category->id)
            ->delete();

        return response()->noContent();
    }
}
