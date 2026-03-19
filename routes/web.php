<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;
use App\Models\Menu;
use App\Services\GenerateMenuPdfService;

Route::get('/menu/{id}/products/show', [MenuController::class, 'showProducts']);
Route::get('/menu-pdf/create', [MenuController::class, 'create']);


Route::get('/test-menu-pdf/{menu}', function (Menu $menu, GenerateMenuPdfService $service) {
    $path = $service->handle($menu);

    return response()->json([
        'message' => 'PDF generated',
        'path' => $path,
        'public_url' => asset("storage/menus/menu-{$menu->id}.pdf"),
    ]);
});

// Preview del Blade del menú directamente en el navegador (sin generar PDF)
Route::get('/preview-menu/{menu}', function (Menu $menu, GenerateMenuPdfService $service) {
    $data = $service->buildData($menu);
    return view('pdf.menu', ['data' => $data]);
});
