<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'OFF25', 'discount_percent' => 25],
            ['code' => 'OFF50', 'discount_percent' => 50],
            ['code' => 'FREE100', 'discount_percent' => 100],
        ];
        foreach ($data as $d) {
            Coupon::updateOrCreate(
                ['code' => $d['code']],
                [
                    'discount_percent' => $d['discount_percent'],
                    'expiry_date' => now()->addYear()->toDateString(),
                    'max_uses' => 100000,
                    'used_count' => 0,
                ]
            );
        }
    }
}

