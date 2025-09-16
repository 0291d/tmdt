<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Insert a default detail row for every product that lacks one
        $missing = DB::table('products as p')
            ->leftJoin('product_details as d', 'd.product_id', '=', 'p.id')
            ->whereNull('d.id')
            ->pluck('p.id');

        foreach ($missing as $productId) {
            DB::table('product_details')->insert([
                'id' => (string) Str::uuid(),
                'product_id' => $productId,
                'width' => 0,
                'length' => 0,
                'height' => 0,
                'origin' => '',
                'finishes' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No-op: do not delete backfilled details
    }
};

