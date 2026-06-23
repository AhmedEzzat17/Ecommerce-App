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

    Route::get('/categories',         [CategoryController::class, 'index']);
    Route::post('/categories',        [CategoryController::class, 'store'])->middleware('admin');
    Route::get('/categories/{id}',    [CategoryController::class, 'show']);
    Route::put('/categories/{id}',    [CategoryController::class, 'update'])->middleware('admin');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('admin');

    Route::get('/products',               [ProductController::class, 'index']);    // ?search=x&sort=price
    Route::get('/products/{id}',          [ProductController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::post('/products',              [ProductController::class, 'store']);
        Route::post('/products/{id}',         [ProductController::class, 'update']);   // POST عشان رفع الصور
        Route::delete('/products/{id}',       [ProductController::class, 'destroy']);
        Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
        Route::get('/deleted-products',       [ProductController::class, 'deleted']);
    });

    Route::get('/cart',               [CartController::class, 'index']);
    Route::post('/cart/add',          [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}',    [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

    // Order Routes
    Route::post('/orders',            [OrderController::class, 'store']);
    Route::get('/orders',             [OrderController::class, 'index']);
});
