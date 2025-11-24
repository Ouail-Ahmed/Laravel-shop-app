<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('home', [
        'season' => 'Autumn',
        'shop_name' => 'The Harvest Basket',
    ]);
});
// Route::resource('products', ProductController::class);
Route::prefix('products')->group(function () {
    // all products
    Route::get('/', [ProductController::class, 'index']);
    // create product form
    Route::get('/create', [ProductController::class, 'create']);
    // one product
    Route::get('/{id}',  [ProductController::class, 'show']);
    // store product
    Route::post('/', [ProductController::class, 'store']);
    // edit product form
    Route::get('/{product}/edit', [ProductController::class, 'edit']);
    // update product
    Route::patch('/{product}', [ProductController::class, 'update']);
    // destroy product
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});

Route::get('/contact', function () {
    return view('contact');
});
