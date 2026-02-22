<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Models\Menu;

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
