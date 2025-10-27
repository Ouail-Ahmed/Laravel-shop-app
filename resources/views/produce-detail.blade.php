<x-layout> <x-slot name="header">{{ $item['name'] }}</x-slot>
<div class="space-y-4">

	<h2 class="text-xl font-bold text-green-700">${{ $item['price'] }}</h2>

	<p class="text-gray-700">{{ $item['description'] }}</p>

	<a href="/products" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800"> &larr; Back to all produce </a>
</div>
</x-layout>
