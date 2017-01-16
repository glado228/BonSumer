<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\MerchantTransactions;

class MerchantTransactionsServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('transactions', function() {
			return new MerchantTransactions();
		});
		$this->app->alias('transactions', 'Bonsum\Services\MerchantTransactions');
	}


	public function provides() {

		return ['Bonsum\Services\MerchantTransactions'];
	}

}
