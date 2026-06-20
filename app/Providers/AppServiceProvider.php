<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Policies\CategoryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\MenuPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Paginator::useBootstrapFive();
    }
}
