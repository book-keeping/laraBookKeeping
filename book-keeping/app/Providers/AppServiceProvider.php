<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\DataProvider\AccountGroupRepositoryInterface::class,
            \App\DataProvider\Eloquent\AccountGroupRepository::class
        );
        $this->app->bind(
            \App\DataProvider\AccountRepositoryInterface::class,
            \App\DataProvider\Eloquent\AccountRepository::class
        );
        $this->app->bind(
            \App\DataProvider\BookRepositoryInterface::class,
            \App\DataProvider\Eloquent\BookRepository::class
        );
        $this->app->bind(
            \App\DataProvider\BudgetRepositoryInterface::class,
            \App\DataProvider\Eloquent\BudgetRepository::class
        );
        $this->app->bind(
            \App\DataProvider\PermissionRepositoryInterface::class,
            \App\DataProvider\Eloquent\PermissionRepository::class
        );
        $this->app->bind(
            \App\DataProvider\SlipEntryRepositoryInterface::class,
            \App\DataProvider\Eloquent\SlipEntryRepository::class
        );
        $this->app->bind(
            \App\DataProvider\SlipRepositoryInterface::class,
            \App\DataProvider\Eloquent\SlipRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
