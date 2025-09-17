<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'discount_percent')) {
                $table->integer('discount_percent')->default(0)->after('coupon_id');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->integer('discount_amount')->default(0)->after('discount_percent');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('orders', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
        });
    }
};

