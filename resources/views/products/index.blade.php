<x-layout>
    <x-slot name="header">Our Fresh Produce</x-slot>

    <ul class="divide-y divide-gray-200">
        @foreach ($produce as $item)
            <li class="py-4 flex justify-between items-center">
                <div>
                    <a href="/produce/{{ $item->id }}" class="text-blue-500 hover:underline">
                        <span class="text-lg font-semibold">{{ $item->name }}</span>
                    </a>
                    <strong class="text-green-600">${{ $item->price }}</strong>
                    <p class="text-sm text-gray-600">From: {{ $item->supplier->name }}</p>
                </div>
                @if ($item->in_stock)
                    <span class="text-xs font-medium text-green-500">In Stock</span>
                @else
                    <span class="text-xs font-medium text-red-500">Out of Stock</span>
                @endif
            </li>
        @endforeach
    </ul>

    {{ $produce->links() }}
</x-layout>
