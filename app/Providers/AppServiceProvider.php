<?php

namespace App\Providers;

use App\Services\SessionCart;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('session.cart', function () {
            return new SessionCart;
        });
    }

    public function boot(): void
    {
        //
    }
}
