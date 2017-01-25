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
        $this->app->singleton('filtry', function($app) {
            return new Filtry;
        });
    }
}
