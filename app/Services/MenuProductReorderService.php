<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenuProductReorderService
{
    public function reorder(Menu $menu, array $products): void
    {
        $ids = [];
        $positions = [];

        foreach ($products as $item) {
            $ids[] = (int) $item['id'];
            $positions[] = (int) $item['position'];
        }

        // Validar posiciones únicas
        $uniquePositions = array_unique($positions);
        if (count($positions) !== count($uniquePositions)) {
            throw ValidationException::withMessages([
                'Products' => ['Positions must be unique.'],
            ]);
        }


        // Validar que todas estén attachadas al menú
        $attachedIds = MenuProduct::query()
            ->where('menu_id', $menu->id)
            ->whereIn('product_id', $ids)
            ->pluck('product_id')
            ->all();

        $attachedIdsInt = [];
        foreach ($attachedIds as $id) {
            $attachedIdsInt[] = (int) $id;
        }

        $missing = array_values(array_diff($ids, $attachedIdsInt));
        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'products' => ['Some Products are not attached to this menu.'],
                'missing_products_ids' => $missing,
            ]);
        }

        DB::transaction(function () use ($menu, $products) {
            foreach ($products as $product) {
                $productId = (int) $product['id'];
                $position = (int) $product['position'];

                MenuProduct::query()
                    ->where('menu_id', $menu->id)
                    ->where('product_id', $productId)
                    ->update(['position' => $position]);
            }
        });
    }
}
