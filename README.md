# 1. Database Setup and Configuration

**Why?** Every professional application needs a database. We'll use **SQLite**—a simple, file-based database perfect for development.

## The `.env` File: Your App's Secret Handshake

- Located in your project's root directory.
- Holds environment-specific settings (database credentials, API keys, etc.).
- **Security:** `.env` is in `.gitignore` by default—never commit secrets!

**SQLite Configuration Example:**

```env
# .env
DB_CONNECTION=sqlite
DB_DATABASE={absolute path to your project database database.sqlite}
```

> **Pro Tip:**
> If `DB_DATABASE` is not an absolute path, Laravel creates the SQLite file relative to `/database`.
> Create the file with:
>
> ```bash
> touch database/database.sqlite
> ```

---

## 2. Building the Schema with Migrations

A **migration** is like a version control commit for your database schema.

### Creating the `products` Migration

Generate the migration file:

```bash
php artisan make:migration create_products_table
```

Define the schema in the generated file:

```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->text('description');
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run the migration:

```bash
php artisan migrate
```

### Visual Inspection

- Use a GUI tool like **TablePlus**.
- Connect to your `database/database.sqlite` file to view your new `products` table.

---

## 3. Eloquent: The Object-Relational Mapper (ORM)

**Eloquent** lets you interact with your database tables as PHP objects.

### Refactoring the Product Model

Edit `app/Models/Product.php`:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
}
```

> You can now **delete** the old static `all()` and `find()` methods!

### Inserting our Objects with Tinker

Tinker is a REPL for PHP where we can try out various commands.

In terminal type  `php artisan tinker`

Then past this:

```php
App\Models\Product::insert([
    ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'in_stock' => true, 'description' => 'Great for roasting!'],
    ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'in_stock' => true, 'description' => 'Perfectly tart and crisp.'],
    ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'in_stock' => false, 'description' => 'A mix of basil, thyme, and rosemary.']
])
```

---

## 4. Populating the Database with Factories & Seeders

**Factories** generate fake, realistic data. **Seeders** use factories to populate your database.

### Creating the Product Factory

Generate the factory:

```bash
php artisan make:factory ProductFactory --model=Product
```

Define fake data in `database/factories/ProductFactory.php`:

```php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'price' => fake()->randomFloat(2, 1, 10),
            'description' => fake()->sentence(),
            'in_stock' => fake()->boolean(80),
        ];
    }
}
```

### Using the Factory with Tinker

```bash
php artisan tinker
```

Inside Tinker:

```php
App\Models\Product::factory()->create(); // One product
App\Models\Product::factory()->count(20)->create(); // 20 products
```

Check TablePlus to see your test data!

---

### Running a Seeder

Generate the seeder:

```bash
php artisan make:seeder ProductSeeder
```

Edit `database/seeders/ProductSeeder.php`:

```php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory(50)->create();
    }
}
```

Run the seeder:

```bash
php artisan migrate:fresh --seed
```

> **Note:**
> Make sure to call your `ProductSeeder` from the main `DatabaseSeeder.php` file.

---

# Eloquent Relationships in Laravel

This guide covers two essential Eloquent relationships: **One-to-Many** (Suppliers and Products) and **Many-to-Many** (Products and Tags).

---

## 1. One-to-Many: Suppliers and Products

A single supplier provides many products, but each product comes from only one supplier.

### A. Creating the Supplier Model and Migration

**1. Generate the Model and Migration**

```bash
php artisan make:model Supplier -mfs
```

This creates the `Supplier` model, its factory, and a migration file.

**2. Define the `suppliers` Table Schema**

Edit the new migration file in `database/migrations/create_suppliers_table`:

```php
public function up(): void
{
    Schema::create('suppliers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

Edit the new factory file also `database/factories/suppliersFactory.php`

```php
<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(2, true),
        ];
    }
}
```

Edit the seeder file

```php
<?php
namespace Database\Seeders;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::factory(20)->create();
    }
}
```

Finally we need to update the Products Factory

```php
<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(2, true),
            'price' => fake()->randomFloat(2, 1, 10),
            'description' => fake()->sentence(),
            'in_stock' => fake()->boolean(80),
   'supplier_id' => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory()->create()->id,
        ];
    }
}
```

**3. Update the `products` Table**

Add a foreign key to link products to suppliers:

```bash
php artisan make:migration add_supplier_id_to_products_table

```

Edit the new migration:

```php
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    });
}
```

> **Note:** `cascadeOnDelete()` ensures that deleting a supplier also deletes its products.

**4. Run the Migrations**

```bash
php artisan migrate:fresh --seed
```

---

### B. Defining the Relationship in Eloquent

**Supplier Model (`app/Models/Supplier.php`):**

```php
class Supplier extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

**Product Model (`app/Models/Product.php`):**

```php
class Product extends Model
{
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
```

**Usage Example (Tinker):**

```bash
# In php artisan tinker

$supplier = App\Models\Supplier::first();
$supplier->products; // All products from this supplier

$product = App\Models\Product::find(5);
$product->supplier; // The supplier for this product
```

---

## 2. Many-to-Many: Products and Tags

A product can have many tags (e.g., "Fruit", "Organic"), and a tag can belong to many products.

### A. Creating the Tag Model and Migration

**1. Generate the Model and Migration**

```bash
php artisan make:model Tag -mfs
```

**2. Define the `tags` Table Schema**

Update the new Tag migration

```php
public function up(): void
{
    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->timestamps();
    });
}
```

Update the Factory file

```php
    public function definition(): array
    {
        return [
            "name" => fake()->lexify('????'),
        ];
    }
```

And the seeder

```php
    public function run(): void
    {
        Tag::factory(10)->create();
    }
```

Finally the database seeder

```php
        $this->call(TagSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ProductSeeder::class);
```

---

### B. Creating the Pivot Table

By convention, the pivot table is named `product_tag`.

**1. Generate the Migration**

```bash
php artisan make:migration create_product_tag_table
```

**2. Define the Pivot Table Schema**

Update the new Tag migration

```php
public function up(): void
{
    Schema::create('product_tag', function (Blueprint $table) {
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
        $table->primary(['product_id', 'tag_id']);
    });
}
```

**3. Run the Migrations**

```bash
php artisan migrate:fresh
```

> `migrate:fresh` rebuilds the database with the new tables.

---

### C. Defining the `belongsToMany` Relationship

**Product Model (`app/Models/Product.php`):**

```php
class Product extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
```

**Tag Model (`app/Models/Tag.php`):**

```php
class Tag extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
```

> **Note:** Using Laravel's naming conventions means Eloquent handles the relationship automatically.

---

### D. Attaching and Using the Relationship

**Usage Example (Tinker):**

```bash
# In php artisan tinker

$product = App\Models\Product::factory()->create(['name' => 'Honeycrisp Apple']);
$tag = App\Models\Tag::factory()->create(['name' => 'Fruit']);

$product->tags()->attach($tag);

// Retrieve the products tags
$product->tags;

// Retrieve all products with a given tag
$tag->products;
```

---

You've now set up both **one-to-many** and **many-to-many** relationships in Eloquent, enabling flexible and powerful data structures in your Laravel application.

### 3. Solving the N+1 Query Problem with Eager Loading

As your application grows, performance becomes critical. One of the most common bottlenecks in database-driven apps is the **N+1 query problem**. Let's see what it is and how to solve it using Eloquent's eager loading.

---

### A. Setting the Scene: Displaying Supplier Names

Suppose you want to show each product's supplier name on your products list page (`resources/views/products.blade.php`). You can access the supplier relationship you defined earlier:

```html
{{-- resources/views/products.blade.php --}}

<x-layout>
    <x-slot name="header">Our Fresh Produce</x-slot>

    <ul class="divide-y divide-gray-200">
        @foreach ($produce as $item)
            <li class="py-4">
                <a href="/products/{{ $item->id }}" class="text-blue-500 hover:underline">
                    <span class="text-lg font-semibold">{{ $item->name }}</span>
                </a>
                {{-- Display the supplier's name --}}
                <p class="text-sm text-gray-600">From: {{ $item->supplier->name }}</p>
            </li>
        @endforeach
    </ul>
</x-layout>
```

If you refresh the page, it works! But behind the scenes, this introduces a major performance issue.

---

### B. Understanding the N+1 Problem

When your route fetches products using `Product::all()`, it runs **one query** to get all products. However, inside the `@foreach` loop, the first time you call `$item->supplier->name`, Eloquent runs a **new query** to fetch the supplier for that product. This happens for every product in the loop.

**Result:**

- 1 query to get all products
- +N queries (one for each product to get its supplier)

If you have 50 products, that's 51 queries! This is the N+1 problem, and it can dramatically slow down your app.

---

### C. Detecting the Problem with Laravel Debugbar

You can't fix a problem you can't see. [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) is a development tool that shows all database queries for each request.

**Install Debugbar:**

```bash
composer require barryvdh/laravel-debugbar --dev
```

**Enable Debug Mode:**
Make sure `APP_DEBUG=true` in your `.env` file.

Now, refresh your products page. A new bar appears at the bottom. Click the "Queries" tab to see the repeated queries, confirming the N+1 problem.

---

### D. The Fix: Eager Loading with `with()`

Eager loading tells Eloquent to fetch related models up front, right after the initial query.

**Update your route in `routes/web.php`:**

```php
// routes/web.php

Route::get('/products', function () {
    // OLD WAY (causes N+1):
    // $products = Product::all();

    // NEW WAY (Eager Loading):
    $products = Product::with('supplier')->get();

    return view('products', [
        'produce' => $products
    ]);
});
```

By adding `->with('supplier')`, you're telling Eloquent:
"Get all products, and also get all their suppliers."

**Result:**

- 1 query to get all products
- 1 query to get all related suppliers

No matter if you have 10 or 10,000 products, only 2 queries are run. Refresh the page and check Debugbar—the query count drops to 2, and your page is much faster.

---

### E. Pro Tip: Disabling Lazy Loading in Development

To proactively catch N+1 issues, you can tell Laravel to throw an error whenever lazy loading is attempted. This forces you to use eager loading where needed.

**Add this to the `boot` method of your `app/Providers/AppServiceProvider.php`:**

```php
// app/Providers/AppServiceProvider.php

use Illuminate\Database\Eloquent\Model;

public function boot(): void
{
    // Only prevent lazy loading in non-production environments
    Model::preventLazyLoading(! app()->isProduction());
}
```

With this in place, if you forget to eager load a relationship, Laravel will stop and tell you exactly where the problem is—helping you write more performant code from the start.

---
---

# Managing Data at Scale with Pagination and Seeders

**Goal:** Learn how to handle large amounts of data gracefully using pagination, and master the developer workflow for populating your database automatically with realistic test data using seeders.

---

## 1. Managing Large Datasets with Pagination

Fetching all products with `Product::all()` works for small datasets, but what if your shop grows to 1,000+ products? Loading everything at once is slow and can crash the browser. The solution: **pagination**.

### A. Implementing Pagination in the Route

Pagination is as simple as swapping the `get()` method for `paginate()`.

**Edit your routes file:** `routes/web.php`

```php
// routes/web.php

use App\Models\Product;

Route::get('/products', function () {
    // Paginate results, showing 12 products per page.
    $products = Product::with('supplier')->paginate(12);

    return view('products', [
        'produce' => $products
    ]);
});
```

Now, your application only fetches 12 products at a time.

---

### B. Displaying Pagination Links in the View

Give users a way to navigate between pages.

**Edit your products view:** `resources/views/products.blade.php`

```html
{{-- resources/views/products.blade.php --}}
<x-layout>
    <x-slot name="header">Our Fresh Produce</x-slot>

    <ul class="divide-y divide-gray-200">
        @foreach ($produce as $item)
            {{-- ... your list item code ... --}}
        @endforeach
    </ul>

    {{-- Render the pagination links --}}
    <div class="mt-6">
        {{ $produce->links() }}
    </div>
</x-layout>
```

Refresh your products page. You'll now see styled pagination links at the bottom. Laravel uses Tailwind CSS for styling by default.

---

### C. Exploring Other Pagination Types

Laravel offers several pagination strategies:

- **Standard Pagination (`paginate()`)**: Shows page numbers (1, 2, 3...).
- **Simple Pagination (`simplePaginate()`)**: More efficient, only "Previous" and "Next" buttons. Ideal when you don't need total page numbers.

    ```php
    $products = Product::with('supplier')->simplePaginate(12);
    ```

- **Cursor Pagination (`cursorPaginate()`)**: Most performant for very large datasets. Uses a "cursor" instead of page numbers, but users can't jump to a specific page.

    ```php
    $products = Product::with('supplier')->cursorPaginate(12);
    ```

- To complete pagination, make sure your `products.blade.php` has this variable

 ```html
    {{ $produce->links() }}
 ```

---

## 2. Automating Data Population with Seeders

Manually creating test data with Tinker is fine for quick tests, but not repeatable. Every time you run `php artisan migrate:fresh`, your database is wiped. **Seeders** solve this by populating the database with initial or test data automatically.

### A. Creating and Using a Seeder

Let's create seeders for products, suppliers, and tags.

**Generate the Seeder Files:**

```bash
php artisan make:seeder SupplierSeeder
php artisan make:seeder TagSeeder
php artisan make:seeder ProductSeeder
```

**Use Factories Inside the Seeders:**
Open each new seeder file in `database/seeders/` and use the corresponding factory to create data.

```php
// database/seeders/SupplierSeeder.php
public function run(): void
{
    \App\Models\Supplier::factory(5)->create();
}

// database/seeders/TagSeeder.php
public function run(): void
{
    \App\Models\Tag::factory(10)->create();
}
```

**Orchestrate with DatabaseSeeder:**
The main `DatabaseSeeder.php` file acts as the entry point. Call your other seeders from here to control the order and keep logic organized.

```php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call individual seeders here
        $this->call([
            SupplierSeeder::class,
            TagSeeder::class,
            // Add ProductSeeder when ready...
        ]);
    }
}
```

---

### B. The Ultimate Workflow Command

Instead of running `migrate` and then `db:seed` separately, do it all at once:

```bash
php artisan migrate:fresh --seed
```

This command will:

- Drop all tables in your database.
- Run all migrations to rebuild the schema.
- Execute your `DatabaseSeeder` class to populate the fresh tables.

Use this command often during development to get a clean, predictable database state.
