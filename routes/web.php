<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Products\SearchProductsController;

Route::get('/search', SearchProductsController::class)->name('products.search');

Route::get('/products/{id}', function ($id) {
    return "Страница товара с ID: " . $id;
})->name('products.show');