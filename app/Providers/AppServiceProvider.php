<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Frontend uses Bootstrap pagination, Filament admin uses Tailwind.
        // Avoid overriding paginator views on Filament routes to keep Livewire pagination working.
        $isFilament = request()->routeIs('filament.*') || request()->is('admin*');
        if (!$isFilament) {
            if (method_exists(Paginator::class, 'useBootstrapFive')) {
                Paginator::useBootstrapFive();
            } else {
                Paginator::useBootstrap();
            }
        }
    }
}
