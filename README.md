
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

- **Purpose:** Blade is Laravel's powerful templating engine that provides a simple syntax for common tasks like defining layouts, using loops, and displaying data.

### B. Creating the Base Layout Component

Instead of traditional layout files, Laravel 11 encourages **View Components**.

1. **Create Component Directory:** Create a new directory: `resources/views/components`.

2. **Create Layout File:** Inside the new directory, create the file `resources/views/components/layout.blade.php`.

3. **Move Common HTML:** Move the common wrapping HTML (e.g., `<html>`, `<head>`, basic `<body>` structure, and the **Navigation** bar) from your `home.blade.php` into this new `layout.blade.php` file.

### C. Defining the Content Injection Point

In `components/layout.blade.php`, we must define where the page-specific content will go.

```html
<body>
 <div class="page-content"> {{ $slot }} </div>
</body>
```

> **Blade Syntax Shortcut:** `{{ $slot }}` is a clean shortcut for the standard PHP `<?php echo $slot; ?>`. Blade automatically handles escaping data to prevent cross-site scripting (XSS) attacks.

### D. Using the Layout in Views

Now, clean up your three main views (`home.blade.php`, `about.blade.php`, `contact.blade.php`). Delete all the wrapping HTML and just reference the layout component.

```html
<x-layout>
 <h1>Welcome to the Fresh Produce Shop!</h1>
 <p>Browse our seasonal selections.</p>
</x-layout>
```

## 3. Creating a Dynamic Navigation Link Component

To make our navigation links reusable and easy to style globally, let's turn them into a component.

### A. Creating the NavLink Component

1. **Create File:** Create `resources/views/components/nav-link.blade.php`.

2. **Add Base Markup:**

3. ```html
   <a {{ $attributes }}> {{ $slot }} </a>

   ```

   > **Understanding `$attributes`:** The `$attributes` variable is automatically available in every component and holds all the HTML attributes (like `href`, `class`, `style`, etc.) passed to the component tag. **Understanding `$slot`:** The content _between_ the opening and closing component tags (e.g., `Home` in `<x-nav-link>Home</x-nav-link>`) is available as `$slot`.

### B. Using the NavLink Component

Update the navigation section in your `resources/views/components/layout.blade.php`:

```html
<nav>
<x-nav-link href="/">Home</x-nav-link>
<x-nav-link href="/products">Products</x-nav-link>
<x-nav-link href="/contact">Contact</x-nav-link>
<x-nav-link href="/seasonal" class="text-green-600">Seasonal Picks</x-nav-link>
</nav>
```

_Refresh your browser. The links should now navigate correctly and accept attributes like `class` or `style`._

---

## 4. Styling the Shop Layout with Tailwind CSS

To make our shop look professional, we'll quickly add basic styling using Tailwind CSS.

### A. Quick Setup via CDN

Since we are focusing on Blade, we'll use the CDN for styling to avoid frontend build steps for now.

Add the following script tag inside the `<head>` of your `resources/views/components/layout.blade.php`:

```html
            <script src="https://cdn.tailwindcss.com"></script>
```

### B. Implementing a Dynamic Page Heading Slot

Real layouts need a unique title for the main content area of each page.

1. **Define Named Slot in Layout:** Update `components/layout.blade.php` to include a spot for a dynamic heading.

2. ```html
   <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
     <h1 class="text-3xl font-bold tracking-tight text-gray-900"> {{ $heading }} </h1>
    </div>
   </header>
   <main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"> {{ $slot }} </div>
   </main>
   ```

   **Pass Data to the Named Slot:** In your views, pass the heading using a special `<x-slot>` tag.

3. ```html
   <x-layout>
    <x-slot name="heading">Home: Fresh Seasonal Produce</x-slot>
    <p>Our featured fruits and vegetables this week...</p>
   </x-layout>
   ```

   >[!Critical]
   > you define a named slot (like `$heading`) in the layout, every view that uses `<x-layout>` **must** define that slot, or Laravel will throw an error about an undefined variable.
   >> Try and use `@isset`
>>
---
