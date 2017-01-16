<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\Localization;

class LocalizationServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('localization', function() {
			return new Localization();
		});

		$this->app->alias('localization', 'Bonsum\Services\Localization');
	}

	public function provides() {
		return ['localization', 'Bonsum\Services\Localization'];
	}

}
