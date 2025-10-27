
**Goal:** Understand and implement Laravel's core templating engine, **Blade**, using layouts and dynamic components to create a reusable structure for the three main shop pages.

---

## 1. Setting Up the Core Routes (3 Pages)

We will define three routes for our main shop pages: **Home** (for the main produce selection), **About Us**, and **Contact**.

### A. Defining the Routes

Ensure your `routes/web.php` file contains the following three routes.

```php
// routes/web.php

Route::get('/', function () {
    return view('home');
});

Route::get('/products', function () {
    return view('products');
});

Route::get('/contact', function () {
    return view('contact');
});
```

### B. Creating and Populating the Views

1. **Rename the Default View:** Rename `resources/views/welcome.blade.php` to `resources/views/home.blade.php`.

2. **Create New Views:** Create two new files: `resources/views/products.blade.php` and `resources/views/contact.blade.php`.

3. **Populate Initial Content:** For now, copy the basic HTML from the original `home.blade.php` into the new files and update the main heading text to confirm the routing is working:

| Page (`.blade.php`) | Heading Text                           |
| ------------------- | -------------------------------------- |
| `home`              | "Welcome to the Fresh Produce Shop!"   |
| `products`          | "Our Story: Fresh from Farm to Table." |
| `contact`           | "Get in Touch with Our Produce Team."  |

### C. Making our navigation bar

Add this to our `home.blade.php` file

```html
<nav>
 <a href="/">Home</a>
 <a href="/products">Products</a>
 <a href="/contact">Contact</a>
</nav>
```

## 2. Introducing Blade for Layout & Components

Manually adding a navigation bar to three files is okay, but imagine 50 pages! We need a reusable **Layout**.

### A. The Blade Templating Engine

- **File Naming:** Rename your views to include the `.blade.php` suffix if you haven't already (e.g., `home.blade.php`).

- **Purpose:** Blade is Laravel's powerful templating engine that provides a simple syntax for common tasks like defining layouts, using loops, and**Goal:** Implement dynamic "active" styling on the navigation bar using Blade components and learn how to pass data (like a list of produce) from a route to a view for rendering.

---

## 1. Implementing Active Navigation Styling

Currently, the navigation links don't visually indicate the current page. We'll fix this using **conditional styling**.

### A. Setting Up the Full-Height Container

To ensure your shop layout fills the viewport and has a consistent background, update the `<html>` and `<body>` tags in your base layout file (`resources/views/components/layout.blade.php`).

```html
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
 <head>
    </head>
 <body class="h-full font-sans"> </body>
</html>
```

### B. Conditional Styling Logic (The Request Helper)

We use Laravel's `request()` helper with the `is()` method to check the current URI.

- **Active Classes (Current Page):** `bg-green-700 text-white` (Shop-specific: Dark green background, white text)

- **Inactive Classes:** `text-gray-300 hover:bg-green-600 hover:text-white` (Lighter text, green hover)

**Example Home Link Logic (Before Component Refactor):**

```html
<x-nav-link  href="/" class="{{ request()->is('/') ? 'bg-green-700 text-black rounded-md px-3 py-2 text-sm font-medium' : 'text-gray-300 hover:bg-green-600 hover:text-white rounded-md px-3 py-2 text-sm font-medium' }}"> Home </x-nav-link>
```

### C. Refactoring into the `<x-nav-link>` Component

We move the conditional logic into our `resources/views/components/nav-link.blade.php` component for reusability.

1. **Declare the Prop:** Use the `@props` directive to declare a custom property called `active`. This is the variable we will use to check the state.

```html
@props(['active' => false]) <a {{ $attributes->merge(['class' => $active
    ? 'bg-green-700 text-black rounded-md px-3 py-2 text-sm font-medium'
    : 'text-gray-300 hover:bg-green-600 hover:text-white rounded-md px-3 py-2 text-sm font-medium'])
}}>
    {{ $slot }}
</a>
```

- **Note on `$attributes->merge()`:** This method intelligently combines the classes defined here with any custom `class` attributes passed when using the component.

- **Using the Component in the Layout:** Update your navigation in `resources/views/components/layout.blade.php`.

```html
 <x-nav-link href="/" active="request()->is('/')">
  Home
 </x-nav-link>

 <x-nav-link href="/products" :active="request()->is('products')">
    Products
 </x-nav-link>
 <x-nav-link href="/about" active="request()->is('about')">
  About Us
 </x-nav-link>

 <x-nav-link href="/contact" :active="request()->is('contact')">
  Contact
 </x-nav-link>
```

>[!note]
>
 Colon Syntax `:` The colon `:active` tells Blade to evaluate the following value (`request()->is('about')`) as a **PHP expression** (which returns a boolean `true` or `false`), rather than treating it as a literal string.

---

## 2. Passing Data from Routes to Views

A static shop isn't useful. We need to fetch and display dynamic lists of produce.

### A. Simple Data Passing (Route to View)

We use the second argument of the `view()` function, an array, to pass data.

**Update `routes/web.php`:**

```php
// routes/web.php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'season' => 'Autumn',
        'shop_name' => 'The Harvest Basket',
    ]);
});
// ... other routes

// Example route for the list of available produce
Route::get('/products', function () {
    return view('products', [
        'produce' => [
            ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'in_stock' => true],
            ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'in_stock' => true],
            ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'in_stock' => false],
        ],
    ]);
});
```

### B. Accessing Data in the View

The array keys become direct variables in your Blade views.

**Example in `resources/views/home.blade.php`:**

```html
<x-layout>
 <x-slot name="header">Welcome to the Shop!</x-slot>
 <h1>Hello, shopper!</h1>
 <p>We are <b>{{ $shop_name }}</b>, featuring fresh <b>{{ $season }} </b> produce.</p>
    <h1>Welcome to the Fresh Produce Shop!</h1>
</x-layout>
```

### C. Looping Complex Data with `@foreach`

For the list of produce, we use the `@foreach` Blade directive.

**1. Create a View:** Create `resources/views/products.blade.php`.
**2. Add Loop Logic:**

```html
<x-layout>
    <x-slot name="header">Our Fresh Produce</x-slot>

    <ul class="divide-y divide-gray-200">
        @foreach ($produce as $item)
            <li class="py-4 flex justify-between items-center">
                <div>
                    <span class="text-lg font-semibold">{{ $item['name'] }}</span>:
                    <strong class="text-green-600">${{ $item['price'] }}</strong>
                </div>

                @if ($item['in_stock'])
                    <span class="text-xs font-medium text-green-500">In Stock</span>
                @else
                    <span class="text-xs font-medium text-red-500">Out of Stock</span>
                @endif
            </li>
        @endforeach
    </ul>
</x-layout>
```

## 3. Dynamic Route Parameters (Product Detail)

To view details for a single product, we use **dynamic routing**.

### A. Defining the Dynamic Route

Update your `routes/web.php` to handle a product ID in the URL.

```php
// routes/web.php (Add this new route)

Route::get('/produce/{id}', function ($id) {
        $allProduce = [
        ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'description' => 'Great for roasting!'],
        ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'description' => 'Perfectly tart and crisp.'],
        ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'description' => 'A mix of basil, thyme, and rosemary.'],
    ];

    //  Use the Collection helper to find the item by its ID
    $item = collect($allProduce)->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

### B. Creating the Detail View

Create `resources/views/produce-detail.blade.php` to display the single item's data.

```html
<x-layout> <x-slot name="header">{{ $item['name'] }}</x-slot>
<div class="space-y-4">
 <h2 class="text-xl font-bold text-green-700">${{ $item['price'] }}</h2>
 <p class="text-gray-700">{{ $item['description'] }}</p>
 <a href="/produce" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800"> &larr; Back to all produce </a>
</div>
</x-layout>
```

### C. Linking to the Detail Page

Make the produce name in your list a clickable link:

**Example in `resources/views/products.blade.php`:**

```html
<a href="/product/{{ $item['id'] }}" class="text-blue-500 hover:underline">
    <span class="text-lg font-semibold">{{ $item['name'] }}</span>
</a>
```

### ### D. Refactoring Our Data (The "Why")

Right now, we have a problem. Our array of produce data is defined in `routes/web.php` for the `/products/{id}` route. But our `/products` route also has its _own_ hard-coded array. This is **data duplication**, and it's a major source of bugs and maintenance headaches.

Let's fix this incrementally.

**Step 1: Centralize the Array**

First, let's move the full array to the top of `routes/web.php` so both routes can share it.

```php
// routes/web.php
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

$allProduce = [
    ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'in_stock' => true, 'description' => 'Great for roasting!'],
    ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'in_stock' => true, 'description' => 'Perfectly tart and crisp.'],
    ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'in_stock' => false, 'description' => 'A mix of basil, thyme, and rosemary.'],
];

// ... other routes ...

Route::get('/products', function () use ($allProduce) {
    return view('products', [
        'produce' => $allProduce
    ]);
});

Route::get('/produce/{id}', function ($id) use ($allProduce) {
    $item = collect($allProduce)->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

### This is better! No more duplication. But... putting all our data in the routes file is still messy. What if 10 routes need this data? The file will become huge. We need to move this logic somewhere dedicated to _data_

---

## 4. Refactoring Data into a Model (The "Proper" Way)

This leads us to the **Model-View-Controller (MVC)** pattern.

### A. Understanding MVC

**Model-View-Controller (MVC)** is a design pattern that separates an application into three interconnected components:

- **Model:** Represents your data and business logic. It's responsible for fetching, storing, and managing data (e.g., our list of produce).

- **View:** The presentation layer; what the user sees. This is our Blade files (e.g., `products.blade.php`).

- **Controller:** Manages user input and interaction, acting as the "traffic cop" between the Model and the View. In simple cases like ours, the **route closure** (`function() { ... }`) acts as the Controller.

Our data array clearly belongs in a **Model**.

### B. Creating the `Product` Model

In Laravel, Models live in the `app/Models` directory.

1. **Create the file:** You can create the file manually at `app/Models/Product.php` or run the Artisan command:
   `bash php artisan make:model Product`.
2. **Add the Logic:** Open the new `app/Models/Product.php` file and add a static method to hold our data.

```php
// app/Models/Product.php

namespace App\Models;

class Product
{
    // This static method returns our hard-coded data.
    // Later, this method will query a real database.
    public static function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'in_stock' => true, 'description' => 'Great for roasting!'],
            ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'in_stock' => true, 'description' => 'Perfectly tart and crisp.'],
            ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'in_stock' => false, 'description' => 'A mix of basil, thyme, and rosemary.'],
        ];
    }
}
```

### C. Refactoring the Routes to Use the Model

Now we can clean up `routes/web.php` significantly.

```php
// routes/web.php
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Models\Product;


// 2. Update the /products route
Route::get('/products', function () {
    return view('products', [
        'produce' => Product::all()
    ]);
});

// 3. Update the /produce/{id} route
Route::get('/produce/{id}', function ($id) {
    $item = collect(Product::all())->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

### ## 5. Refining the Model with a "Find" Method

Our `/products` route looks great, but the `/produce/{id}` route is still doing its own data-finding logic. That logic _also_ belongs in the Model.

### A. Adding a `find` Method to the Model

Let's edit `app/Models/Product.php` and add a new method specifically for finding one item.

> [!hint] We'll use a handy Laravel helper called `Arr::first`. Don't forget to import it at the top of the file: `use Illuminate\Support\Arr;`

```php
// app/Models/Product.php
namespace App\Models;
use Illuminate\Support\Arr;

class Product
{
    public static function find(int $id): ?array
    {
        return Arr::first(self::all(), fn($product) => $product['id'] == $id);
    }
}
```

### B. Refactoring the Detail Route (Again)

Now, let's make our `/produce/{id}` route beautifully simple.

```php
// routes/web.php

Route::get('/produce/{id}', function ($id) {
    $item = Product::find($id);

    return view('produce-detail', ['item' => $item]);
});
```

## 6. Handling the "Sad Path"

We have one last problem. What happens if you visit `/produce/99`?

`Product::find(99)` will return `null`. Our `produce-detail.blade.php` view will then try to access `$item['name']` on `null`, causing an "Attempt to read property 'name' on null" error. This is a bad user experience.

This is the **"Sad Path"**—when things don't go as expected.

We can gracefully handle this using Laravel's `abort` helper.

### A. Implementing `abort(404)`

Let's update our final route to be "production-ready."

```php

Route::get('/produce/{id}', function ($id) {
    $item = Product::find($id);

    if (! $item) {
        abort(404);
    }

    return view('produce-detail', ['item' => $item]);
});
```

Now, if a user requests a product that doesn't exist, they will see a professional "404 Not Found" page instead of a scary application error.
 displaying data
---
