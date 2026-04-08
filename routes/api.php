<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Products\Search\AutocompleteController;
use App\Http\Controllers\Api\Products\Search\CategoryTreeController;
use App\Http\Controllers\Api\Products\Search\SearchProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::get('/products/autocomplete', AutocompleteController::class);

Route::get('/products/search', SearchProductsController::class)->name('products.search');

Route::get('/products/category-tree', CategoryTreeController::class)->name('products.category-tree');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});
