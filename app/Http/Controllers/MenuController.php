<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Services\GenerateMenuPdfService;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index()
    {
        return MenuResource::collection(Menu::paginate(20));
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->validated());

        return (new MenuResource($menu))->response()->setStatusCode(201);
    }

    public function show(Menu $menu)
    {
        return new MenuResource($menu);
    }

    public function update(UpdateMenuRequest $request, Menu $menu, GenerateMenuPdfService $pdfService)
    {
        $menu->update($request->validated());

        try {
            $pdfService->handle($menu);
        } catch (\Throwable $e) {
            Log::error("PDF generation failed for menu {$menu->id}: {$e->getMessage()}");
        }

        return new MenuResource($menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->noContent(204);
    }
}
