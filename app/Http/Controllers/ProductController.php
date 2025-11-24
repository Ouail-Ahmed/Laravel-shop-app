<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index', ['produce' => Product::with('supplier')->Paginate(8)],);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', ['suppliers' => Supplier::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'min:3'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'min:10'],
            'in_stock' => ['nullable', 'boolean'],
            'supplier_id' => ['required', 'exists:suppliers,id']
        ]);

        $attributes['in_stock'] = $request->has('in_stock');

        Product::create($attributes);

        return redirect('/products');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Product::find($id);
        if (!$item) {
            abort(404);
        }
        return view('products.show', ['item' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', ['suppliers' => Supplier::all(), 'product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $attributes = request()->validate([
            'name' => ['required', 'string', 'min:3'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'min:1'],
            'in_stock' => ['nullable', 'boolean'],
            'supplier_id' => ['required', 'exists:suppliers,id']
        ]);

        $attributes['in_stock'] = isset($attributes['in_stock']);

        $product->update($attributes);

        return redirect('/products/' . $product->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect('/products');
    }
}
