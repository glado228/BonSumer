<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\Resource;


class ResourceServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app['validator']->extend('noscripttags', function($attribute, $value) {
			return (preg_match('/'. Resource::SANITIZE_REGEXP .'/', $value) === 0);
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('resources', function() {
			return new Resource();
		});

		$this->app->alias('resources', 'Bonsum\Services\Resources');
		$this->app->instance('path.media', $this->app->basePath().'/resources/media');
	}

}
