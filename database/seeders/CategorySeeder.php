<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Bed','Armchair','Sofa','Chair','Table'];
        foreach ($names as $name) {
            $exists = DB::table('categories')->where('name', $name)->exists();
            if (!$exists) {
                DB::table('categories')->insert([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
