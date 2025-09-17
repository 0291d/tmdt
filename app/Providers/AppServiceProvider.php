<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Frontend uses Bootstrap pagination; Filament admin (under /admin) keeps Tailwind/Livewire.
        // Use a robust check that works even when the app is served under a subdirectory (e.g. /tmdt/public/admin/...)
        $path = request()->getPathInfo(); // e.g. "/tmdt/public/admin/products"
        $isAdminPath = (bool) preg_match('~/(admin)(/|$)~', $path);
        if (!$isAdminPath) {
            if (method_exists(Paginator::class, 'useBootstrapFive')) {
                Paginator::useBootstrapFive();
            } else {
                Paginator::useBootstrap();
            }
        }

        // Ensure Livewire assets load correctly when app is served from a subdirectory (e.g. /tmdt/public)
        // Livewire v2 uses `livewire.asset_url` as a prefix for its script/style routes.
        $baseUrl = rtrim((string) request()->getBaseUrl(), '/'); // "/tmdt/public" or ""
        if (!empty($baseUrl)) {
            Config::set('livewire.asset_url', $baseUrl);
        }
    }
}
