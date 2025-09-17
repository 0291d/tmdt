<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'created_at')) {
                $table->timestamp('created_at')->useCurrent()->after('price');
            }
            if (!Schema::hasColumn('order_items', 'updated_at')) {
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
                if (Schema::hasColumn('order_items', 'created_at')) {
                    $table->dropColumn('created_at');
                }
        });
    }
};
