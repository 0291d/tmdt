<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'), 
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'norman',
                'email' => 'norman@gmail.com',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
