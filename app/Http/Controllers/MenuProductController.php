<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuProduct\ReorderMenuProductRequest;
use App\Http\Requests\MenuProduct\StoreMenuProductRequest;
use App\Http\Requests\MenuProduct\UpdateMenuProductRequest;
use App\Http\Resources\MenuProductResource;
use App\Models\Menu;
use App\Models\MenuProduct;
use App\Models\Product;
use App\Services\GenerateMenuPdfService;
use App\Services\MenuProductReorderService;
use Illuminate\Support\Facades\Log;

class MenuProductController extends Controller
{
    public function __construct(private GenerateMenuPdfService $pdfService) {}

    /**
     * List all products configured for the given menu,
     * including their variants.
     */
    public function index(Menu $menu)
    {
        $menu->load('products.variants');

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
        ]);

        try {
            $this->pdfService->handle($menu);
        } catch (\Throwable $e) {
            Log::error("PDF generation failed for menu {$menu->id}: {$e->getMessage()}");
        }

        return response()->json($menuProduct, 201);
    }

    /**
     * Reorder products inside a menu.
     */
    public function reorder(ReorderMenuProductRequest $request, Menu $menu, MenuProductReorderService $service)
    {
        $service->reorder($menu, $request->validated()['products']);

        try {
            $this->pdfService->handle($menu);
        } catch (\Throwable $e) {
            Log::error("PDF generation failed for menu {$menu->id}: {$e->getMessage()}");
        }

        return response()->noContent();
    }

    /**
     * custom_price was removed; this endpoint is kept for route compatibility
     * but no longer has anything to update.
     */
    public function update(UpdateMenuProductRequest $request, Menu $menu, Product $product)
    {
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

        try {
            $this->pdfService->handle($menu);
        } catch (\Throwable $e) {
            Log::error("PDF generation failed for menu {$menu->id}: {$e->getMessage()}");
        }

        return response()->noContent();
    }
}
