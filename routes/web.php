<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Products\SearchProductsController;

Route::get('/search', SearchProductsController::class)->name('products.search');

