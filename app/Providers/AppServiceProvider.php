<?php

namespace App\Providers;

use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\MenuRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
