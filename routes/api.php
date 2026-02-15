<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuProductController;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('products', ProductController::class);
Route::apiResource('promotions', PromotionController::class);
Route::apiResource('menus', MenuController::class);

Route::post('contact-requests', [ContactRequestController::class, 'store']);

Route::apiResource('contact-requests', ContactRequestController::class)
    ->only(['index', 'show', 'update', 'destroy']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('contact-requests', ContactRequestController::class)
//         ->only(['index','show','update','destroy']);
// });

Route::prefix('menus/{menu}')->group(function () {
    Route::get('categories', [MenuCategoryController::class, 'index']);
    Route::post('categories', [MenuCategoryController::class, 'store']);
    Route::put('categories/order', [MenuCategoryController::class, 'reorder']);
    Route::delete('categories/{category}', [MenuCategoryController::class, 'destroy']);

    Route::get('products', [MenuProductController::class, 'index']);
    Route::post('products', [MenuProductController::class, 'store']);
    Route::put('products/order', [MenuProductController::class, 'reorder']);
    Route::put('products/{product}', [MenuProductController::class, 'update']);
    Route::delete('products/{product}', [MenuProductController::class, 'destroy']);
});


// Route::prefix('promotions/{promotion}')->group(function () {
//     Route::post('products', [PromotionProductController::class, 'store']);
//     Route::delete('products/{product}', [PromotionProductController::class, 'destroy']);
// });
