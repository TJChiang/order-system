<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderRepository as OrderRepositoryContract;
use App\Repositories\Contracts\ProductRepository as ProductRepositoryContract;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            OrderRepositoryContract::class,
            ProductRepositoryContract::class,
        ];
    }

    public function register(): void
    {
        $this->app->singleton(
            OrderRepositoryContract::class,
            fn () => $this->app->make(OrderRepository::class),
        );

        $this->app->singleton(
            ProductRepositoryContract::class,
            fn () => $this->app->make(ProductRepository::class),
        );
    }

    public function boot(): void
    {
        //
    }
}
