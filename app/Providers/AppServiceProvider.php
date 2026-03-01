<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
//use Illuminate\Pagination\Paginator;
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

                //Province
                $this->app->singleton(
                        \App\Repositories\Province\ProvinceRepositoryInterface::class,
                        \App\Repositories\Province\ProvinceEloquentRepository::class
                );
                //District
                $this->app->singleton(
                        \App\Repositories\District\DistrictRepositoryInterface::class,
                        \App\Repositories\District\DistrictEloquentRepository::class
                );
                //Ward
                $this->app->singleton(
                        \App\Repositories\Ward\WardRepositoryInterface::class,
                        \App\Repositories\Ward\WardEloquentRepository::class
                );
                //User
                $this->app->singleton(
                        \App\Repositories\User\UserRepositoryInterface::class,
                        \App\Repositories\User\UserEloquentRepository::class
                );
                //Category
                $this->app->singleton(
                        \App\Repositories\Category\CategoryRepositoryInterface::class,
                        \App\Repositories\Category\CategoryEloquentRepository::class
                );
                //Product
                $this->app->singleton(
                        \App\Repositories\Product\ProductRepositoryInterface::class,
                        \App\Repositories\Product\ProductEloquentRepository::class
                );
                $this->app->singleton(
                        \App\Repositories\Product\ProductOptionRepositoryInterface::class,
                        \App\Repositories\Product\ProductOptionEloquentRepository::class
                );
                $this->app->singleton(
                        \App\Repositories\Rating\RatingRepositoryInterface::class,
                        \App\Repositories\Rating\RatingEloquentRepository::class
                );
                //Inventory
                $this->app->singleton(
                        \App\Repositories\Inventory\InventoryRepositoryInterface::class,
                        \App\Repositories\Inventory\InventoryEloquentRepository::class
                );
                // Post
                $this->app->singleton(
                        \App\Repositories\Post\PostRepositoryInterface::class,
                        \App\Repositories\Post\PostEloquentRepository::class
                );
                // Media
                $this->app->singleton(
                        \App\Repositories\Media\PositionRepositoryInterface::class,
                        \App\Repositories\Media\PositionEloquentRepository::class
                );
                $this->app->singleton(
                        \App\Repositories\Media\PhotoRepositoryInterface::class,
                        \App\Repositories\Media\PhotoEloquentRepository::class
                );
                // Attribute Set
                $this->app->singleton(
                        \App\Repositories\Store\AttributeSetRepositoryInterface::class,
                        \App\Repositories\Store\AttributeSetEloquentRepository::class
                );
                //Order
                $this->app->singleton(
                        \App\Repositories\Order\OrderRepositoryInterface::class,
                        \App\Repositories\Order\OrderEloquentRepository::class
                );
                $this->app->singleton(
                        \App\Repositories\Order\OrderItemsRepositoryInterface::class,
                        \App\Repositories\Order\OrderItemsEloquentRepository::class
                );
                $this->app->singleton(
                        \App\Repositories\Order\OrderTimelinesRepositoryInterface::class,
                        \App\Repositories\Order\OrderTimelinesEloquentRepository::class
                );
                //Contact
                $this->app->singleton(
                        \App\Repositories\Contact\ContactRepositoryInterface::class,
                        \App\Repositories\Contact\ContactEloquentRepository::class
                );
                //Coupon
                $this->app->singleton(
                        \App\Repositories\Coupon\CouponRepositoryInterface::class,
                        \App\Repositories\Coupon\CouponEloquentRepository::class
                );
                //Product Sale
                $this->app->singleton(
                        \App\Repositories\Sales\SalesRepositoryInterface::class,
                        \App\Repositories\Sales\SalesEloquentRepository::class
                );
                //Tier Sale
                $this->app->singleton(
                        \App\Repositories\TierPrice\TierPriceRepositoryInterface::class,
                        \App\Repositories\TierPrice\TierPriceEloquentRepository::class
                );
                //Tier Sale
                $this->app->singleton(
                        \App\Repositories\Store\ProductOptionEntriesInterface::class,
                        \App\Repositories\Store\ProductOptionEntriesEloquentRepository::class
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
                //        Paginator::useBootstrapFive();
                //        Paginator::useBootstrapFour();
                Schema::defaultStringLength(191);
        }
}
