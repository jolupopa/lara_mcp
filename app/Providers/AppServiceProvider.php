<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Boost;
use Custom\Support\Cline;


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
         Boost::registerCodeEnvironment('cline', Cline::class);
    }
}
