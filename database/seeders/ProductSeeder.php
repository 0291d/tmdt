<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn($name) => DB::table('categories')->where('name', $name)->value('id');

        $products = [
            [
                'id' => (string) Str::uuid(),
                'category_id' => $cat('Bed'),
                'name' => 'Malm Bed Frame',
                'brand' => 'IKEA',
                'description' => 'Modern Scandinavian style bed frame from IKEA.',
                'price' => 450.00,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'category_id' => $cat('Armchair'),
                'name' => 'Imola Armchair',
                'brand' => 'BoConcept',
                'description' => 'Iconic Danish design armchair from BoConcept.',
                'price' => 1200.00,
                'stock' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'category_id' => $cat('Sofa'),
                'name' => 'Ektorp Sofa',
                'brand' => 'IKEA',
                'description' => 'Comfortable 3-seat fabric sofa.',
                'price' => 980.00,
                'stock' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'category_id' => $cat('Chair'),
                'name' => 'STEFAN Chair',
                'brand' => 'IKEA',
                'description' => 'Solid wood dining chair.',
                'price' => 49.00,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'category_id' => $cat('Table'),
                'name' => 'LACK Coffee Table',
                'brand' => 'IKEA',
                'description' => 'Minimalist coffee table.',
                'price' => 29.00,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert(array_filter($products, fn($p) => !empty($p['category_id'])));
    }
}
