<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Route;

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
        // if (!$this->app->isLocal()) {
            // $this->app['request']->server->set('HTTPS', true);
    //    }
    if (!$this->app->isLocal()) {
        //else register your services you require for production
        $this->app['request']->server->set('HTTPS', true);
    }
    }
}
