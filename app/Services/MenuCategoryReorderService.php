<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenuCategoryReorderService
{
    public function reorder(Menu $menu, array $categories): void
    {
        // Extraer ids y posiciones
        $ids = [];
        $positions = [];

        foreach ($categories as $item) {
            $ids[] = (int) $item['id'];
            $positions[] = (int) $item['position'];
        }

        // Validar posiciones únicas
        $uniquePositions = array_unique($positions);
        if (count($positions) !== count($uniquePositions)) {
            throw ValidationException::withMessages([
                'categories' => ['Positions must be unique.'],
            ]);
        }

        // Validar que todas estén attachadas al menú
        $attachedIds = MenuCategory::query()
            ->where('menu_id', $menu->id)
            ->whereIn('category_id', $ids)
            ->pluck('category_id')
            ->all();

        $attachedIdsInt = [];
        foreach ($attachedIds as $id) {
            $attachedIdsInt[] = (int) $id;
        }

        $missing = array_values(array_diff($ids, $attachedIdsInt));
        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'categories' => ['Some categories are not attached to this menu.'],
                'missing_category_ids' => $missing,
            ]);
        }

        // Aplicar updates en transacción
        DB::transaction(function () use ($menu, $categories) {
            foreach ($categories as $item) {
                $categoryId = (int) $item['id'];
                $position = (int) $item['position'];

                MenuCategory::query()
                    ->where('menu_id', $menu->id)
                    ->where('category_id', $categoryId)
                    ->update(['position' => $position]);
            }
        });
    }
}
