<?php

use Illuminate\Support\Facades\Route;

Route::get('/products/{id}', function ($id) {
    return "Страница товара с ID: " . $id;
})->name('products.show');