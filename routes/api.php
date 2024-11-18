<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
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

Route::get('admin/orders', [AdminController::class, 'get_all_orders'])->middleware(['auth:api', 'role:adm']);
Route::get('admin/orders/{id}', [AdminController::class, 'get_order_by_id'])->middleware(['auth:api', 'role:adm']);
Route::put('admin/orders/{id}', [AdminController::class, 'update_order_status'])->middleware(['auth:api', 'role:adm']);

Route::get('admin/users', [AdminController::class, 'get_all_users'])->middleware(['auth:api', 'role:adm']);
Route::get('admin/users/{user_id}/orders/', [AdminController::class, 'get_order_by_user_id'])->middleware(['auth:api', 'role:adm']);
Route::delete('admin/users/{id}', [AdminController::class, 'delete_user'])->middleware(['auth:api', 'role:adm']);



Route::middleware('log.request.response')->get('cart', [CartController::class, 'index'])->middleware(['auth:api', 'role:usr']);
Route::middleware('log.request.response')->post('cart/items', [CartController::class, 'store'])->middleware(['auth:api', 'role:usr']);
Route::middleware('log.request.response')->put('cart/items/{id}', [CartController::class, 'update'])->middleware(['auth:api', 'role:usr']);
Route::middleware('log.request.response')->delete('cart/items/{id}', [CartController::class, 'destroy'])->middleware(['auth:api', 'role:usr']);

Route::middleware('log.request.response')->get('orders', [OrderController::class, 'index'])->middleware(['auth:api', 'role:usr']);
Route::middleware('log.request.response')->post('orders', [OrderController::class, 'store'])->middleware(['auth:api', 'role:usr']);
Route::middleware('log.request.response')->get('orders/{id}', [OrderController::class, 'show'])->middleware(['auth:api', 'role:usr']);





