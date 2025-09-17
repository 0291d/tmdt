<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql'])) {
            return; // Only applies to MySQL/MariaDB-compatible drivers
        }

        $domainTables = [
            'users',
            'personal_access_tokens',
            'categories',
            'products',
            'customers',
            'news',
            'images',
            'coupons',
            'orders',
            'product_details',
            'comments',
            'contacts',
            'wishlists',
            'order_items',
        ];

        // laravel-admin tables (use configured names if present)
        $adminTables = [
            config('admin.database.users_table'),
            config('admin.database.roles_table'),
            config('admin.database.permissions_table'),
            config('admin.database.menu_table'),
            config('admin.database.role_users_table'),
            config('admin.database.role_permissions_table'),
            config('admin.database.user_permissions_table'),
            config('admin.database.role_menu_table'),
            config('admin.database.operation_log_table'),
        ];

        $tables = array_filter(array_unique(array_merge($domainTables, $adminTables)));

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            try {
                if (Schema::hasColumn($table, 'created_at')) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
                }
            } catch (\Throwable $e) {
                // ignore and continue
            }

            try {
                if (Schema::hasColumn($table, 'updated_at')) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                }
            } catch (\Throwable $e) {
                // ignore and continue
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql'])) {
            return;
        }

        $domainTables = [
            'users',
            'personal_access_tokens',
            'categories',
            'products',
            'customers',
            'news',
            'images',
            'coupons',
            'orders',
            'product_details',
            'comments',
            'contacts',
            'wishlists',
            'order_items',
        ];

        $adminTables = [
            config('admin.database.users_table'),
            config('admin.database.roles_table'),
            config('admin.database.permissions_table'),
            config('admin.database.menu_table'),
            config('admin.database.role_users_table'),
            config('admin.database.role_permissions_table'),
            config('admin.database.user_permissions_table'),
            config('admin.database.role_menu_table'),
            config('admin.database.operation_log_table'),
        ];

        $tables = array_filter(array_unique(array_merge($domainTables, $adminTables)));

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            try {
                if (Schema::hasColumn($table, 'created_at')) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL");
                }
            } catch (\Throwable $e) {
                // ignore and continue
            }

            try {
                if (Schema::hasColumn($table, 'updated_at')) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL");
                }
            } catch (\Throwable $e) {
                // ignore and continue
            }
        }
    }
};

