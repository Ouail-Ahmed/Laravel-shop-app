<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Product
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Sweet Potato', 'price' => 2.99, 'in_stock' => true, 'description' => 'Great for roasting!'],
            ['id' => 2, 'name' => 'Granny Smith Apple', 'price' => 1.50, 'in_stock' => true, 'description' => 'Perfectly tart and crisp.'],
            ['id' => 3, 'name' => 'Fresh Herbs Bundle', 'price' => 4.50, 'in_stock' => false, 'description' => 'A mix of basil, thyme, and rosemary.']
        ];
    }
    public static function find(int $id): array
    {
        $product = Arr::first(self::all(), fn($p) => $p['id'] == $id);

        return $product;
    }
}
