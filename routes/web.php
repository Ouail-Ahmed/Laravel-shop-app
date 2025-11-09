<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Supplier;

Route::get('/', function () {
    return view('home', [
        'season' => 'Autumn',
        'shop_name' => 'The Harvest Basket',
    ]);
});

Route::get('/products', function () {
    return view('products.index', ['produce' => Product::with('supplier')->cursorPaginate(5)],);
});
Route::get('/products/create', function () {
    return view('products.create', ['suppliers' => Supplier::all()]);
});

Route::post('/products', function () {
    // Logic to handle the form submission will go here
    // dd(request()->all());
    request()->validate([
        'name' => ['required', 'min:3'],
        'price' => ['required', 'numeric'],
    ]);

    // 2. Create the new product
    \App\Models\Product::create([
        'name' => request('name'),
        'price' => request('price'),
        'description' => 'Default description',
        'supplier_id' => 1,
    ]);

    // 3. Redirect the user
    return redirect('/products');
});

Route::get('/products/{product}/edit', function (Product $product) {
    return view('products.edit', ['suppliers' => Supplier::all(), 'product' => $product]);
});

Route::get('/products/{id}', function ($id) {
    $item = Product::find($id);
    if (!$item) {
        abort(404);
    }
    return view('products.show', ['item' => $item]);
});

// Update the product
Route::patch('/product/{product}', function (Product $product) {
    request()->validate([
        'name' => ['required', 'min:3'],
        'price' => ['required', 'numeric'],
    ]);

    $product->update([
        'name' => request('name'),
        'price' => request('price'),
    ]);

    return redirect('/products/' . $product->id);
});

Route::delete('/products/{product}', function (\App\Models\Product $product) {
    $product->delete();
    return redirect('/products');
});

Route::get('/contact', function () {
    return view('contact');
});
