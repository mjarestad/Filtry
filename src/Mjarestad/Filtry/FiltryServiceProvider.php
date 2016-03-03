<?php

namespace Mjarestad\Filtry;

use Illuminate\Support\ServiceProvider;

class FiltryServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		$this->package('mjarestad/filtry');
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		// Register 'filtry' instance container to our Filtry object
		$this->app['filtry'] = $this->app->share(function($app) {
            return new Filtry;
        });
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}