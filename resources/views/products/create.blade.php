<x-layout>
    <x-slot name="header">Add a New Product</x-slot>

    <form method="POST" action="/products" class="max-w-lg mx-auto mt-8 bg-white p-6 rounded-lg shadow">
        @csrf

        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" id="name" required
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input step="0.01" min="0" name="price" id="price" required
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" required
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="in_stock" id="in_stock" value="1"
                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <label for="in_stock" class="ml-2 block text-sm text-gray-700">In Stock</label>
            </div>

            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                <select name="supplier_id" id="supplier_id" required
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Select a supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" class="bg-white text-black">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="w-full rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">Save
                Product</button>
        </div>
    </form>
</x-layout>
