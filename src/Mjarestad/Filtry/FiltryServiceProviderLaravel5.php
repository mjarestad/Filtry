<?php

namespace Mjarestad\Filtry;

use Illuminate\Support\ServiceProvider;

class FiltryServiceProviderLaravel5 extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app['filtry'] = $this->app->share(function($app) {
            return new Filtry;
        });
    }
}