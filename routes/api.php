<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('products', ProductController::class);
Route::apiResource('promotions', PromotionController::class);
