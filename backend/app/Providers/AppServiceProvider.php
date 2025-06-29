<?php

namespace App\Providers;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('cancel-travel-order', function (User $user, TravelOrder $order) {
            return $user->is_admin && $order->status !== TravelOrderStatus::Approved;
        });
        Gate::define('approve-travel-order', function (User $user, TravelOrder $order) {
            return $user->is_admin;
        });
    }
}
