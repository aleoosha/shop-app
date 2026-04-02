<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Products\Search\AutocompleteController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Products\Search\CategoryTreeController;
use App\Http\Controllers\Api\Products\Search\SearchProductsController;

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::get('/products/autocomplete', AutocompleteController::class);

Route::get('/products/search', SearchProductsController::class)->name('products.search');

Route::get('/products/category-tree', CategoryTreeController::class)->name('products.category-tree');