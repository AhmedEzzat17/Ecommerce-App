<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',       [AuthController::class, 'logout']);

    Route::get('/profile',       [AuthController::class, 'profile']);

    Route::get('/dashboard', [ProductController::class, 'dashboard'])->middleware('admin');

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
        Route::get('/deleted-products', [ProductController::class, 'deleted']);
    });
    Route::get('/products', [ProductController::class, 'index']);    // ?search=x&sort=price
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::get('/cart',               [CartController::class, 'index']);
    Route::post('/cart/add',          [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}',    [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

    Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
});
