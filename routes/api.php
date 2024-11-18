<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});


Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);

Route::post('products', [ProductController::class, 'store'])->middleware(['auth:api', 'role:adm']);
Route::put('products/{id}', [ProductController::class, 'update'])->middleware(['auth:api', 'role:adm']);
Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware(['auth:api', 'role:adm']);

Route::get('cart', [CartController::class, 'index'])->middleware(['auth:api', 'role:usr']);
Route::post('cart/items', [CartController::class, 'store'])->middleware(['auth:api', 'role:usr']);
Route::put('cart/items/{id}', [CartController::class, 'update'])->middleware(['auth:api', 'role:usr']);
Route::delete('cart/items/{id}', [CartController::class, 'destroy'])->middleware(['auth:api', 'role:usr']);

