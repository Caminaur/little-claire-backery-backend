<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\PromotionProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

// --- Públicos ---
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('products/{product}/variants', ProductVariantController::class)->only(['index', 'show']);
Route::get('products/{product}/variants/{variant}/images', [ProductImageController::class, 'index']);
Route::apiResource('promotions', PromotionController::class)->only(['index', 'show']);
Route::get('promotions/{promotion}/products', [PromotionProductController::class, 'index']);
Route::apiResource('menus', MenuController::class)->only(['index', 'show']);

Route::prefix('menus/{menu}')->group(function () {
    Route::get('categories', [MenuCategoryController::class, 'index']);
    Route::get('products', [MenuProductController::class, 'index']);
});

Route::post('contact-requests', [ContactRequestController::class, 'store'])->middleware('throttle:10,1');

// --- Auth ---
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
    });
});

// --- Admin (requiere autenticación) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::put('categories/reorder', [CategoryController::class, 'reorder']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('products/{product}/variants', ProductVariantController::class)->only(['store', 'update', 'destroy']);
    Route::post('products/{product}/variants/{variant}/images', [ProductImageController::class, 'store']);
    Route::put('products/{product}/variants/{variant}/images/{image}', [ProductImageController::class, 'update']);
    Route::delete('products/{product}/variants/{variant}/images/{image}', [ProductImageController::class, 'destroy']);
    Route::apiResource('promotions', PromotionController::class)->only(['store', 'update', 'destroy']);
    Route::post('promotions/{promotion}/products', [PromotionProductController::class, 'store']);
    Route::delete('promotions/{promotion}/products/{product}', [PromotionProductController::class, 'destroy']);
    Route::apiResource('menus', MenuController::class)->only(['store', 'update', 'destroy']);

    Route::apiResource('contact-requests', ContactRequestController::class)->only(['index', 'show', 'update', 'destroy']);

    Route::prefix('menus/{menu}')->group(function () {
        Route::post('categories', [MenuCategoryController::class, 'store']);
        Route::put('categories/order', [MenuCategoryController::class, 'reorder']);
        Route::delete('categories/{category}', [MenuCategoryController::class, 'destroy']);

        Route::post('products', [MenuProductController::class, 'store']);
        Route::put('products/order', [MenuProductController::class, 'reorder']);
        Route::put('products/{product}', [MenuProductController::class, 'update']);
        Route::delete('products/{product}', [MenuProductController::class, 'destroy']);
    });
});
