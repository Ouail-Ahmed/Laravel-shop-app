<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;


Route::get('/', function () {
    return view('home', [
        'season' => 'Autumn',
        'shop_name' => 'The Harvest Basket',
    ]);
});

Route::get('/products', function () {
    return view('products', ['produce' => Product::all()],);
});
Route::get('/product/{id}', function ($id) {

    //  Use the Collection helper to find the item by its ID
    $item = Product::find($id);
    if (!$item) {
        abort(404);
    }
    return view('produce-detail', ['item' => $item]);
});
Route::get('/contact', function () {
    return view('contact');
});
