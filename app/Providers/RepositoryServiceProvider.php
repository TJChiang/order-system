<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderItemRepository as OrderItemRepositoryContract;
use App\Repositories\Contracts\OrderRepository as OrderRepositoryContract;
use App\Repositories\Contracts\ProductRepository as ProductRepositoryContract;
use App\Repositories\Contracts\ShipmentRepository as ShipmentRepositoryContract;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShipmentRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            OrderRepositoryContract::class,
            OrderItemRepositoryContract::class,
            ProductRepositoryContract::class,
            ShipmentRepositoryContract::class,
        ];
    }

    public function register(): void
    {
        $this->app->singleton(
            OrderRepositoryContract::class,
            fn () => $this->app->make(OrderRepository::class),
        );

        $this->app->singleton(
            OrderItemRepositoryContract::class,
            fn () => $this->app->make(OrderItemRepository::class),
        );

        $this->app->singleton(
            ProductRepositoryContract::class,
            fn () => $this->app->make(ProductRepository::class),
        );

        $this->app->singleton(
            ShipmentRepositoryContract::class,
            fn () => $this->app->make(ShipmentRepository::class),
        );
    }

    public function boot(): void
    {
        //
    }
}
