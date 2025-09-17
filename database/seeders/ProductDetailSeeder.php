<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductDetailSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->get();

        DB::table('product_details')->insert([
            [
                'id' => (string) Str::uuid(),
                'product_id' => $products[0]->id,
                'width' => 160,
                'length' => 200,
                'height' => 50,
                'origin' => 'Sweden',
                'finishes' => 'Oak veneer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'product_id' => $products[1]->id,
                'width' => 90,
                'length' => 85,
                'height' => 110,
                'origin' => 'Denmark',
                'finishes' => 'Full-grain leather',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
