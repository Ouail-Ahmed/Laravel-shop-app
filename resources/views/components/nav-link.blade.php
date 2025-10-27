@props(['active' => false]) <a {{ $attributes->merge(['class' => $active
    ? 'bg-green-700 text-black rounded-md px-3 py-2 text-sm font-medium'
    : 'text-gray-300 hover:bg-green-600 hover:text-white rounded-md px-3 py-2 text-sm font-medium'])
}}>
    {{ $slot }}
</a>
