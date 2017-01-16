<?php namespace Bonsum\Bootstrap;

use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as DefaultConfigureLogging;

class ConfigureLogging extends DefaultConfigureLogging {

	/*
		This class extends DefaultConfigureLogging with the ability of specifying
		a minimum log level from config('app.log_level')

		Class is registered in Bonsum\{Http|Console}\Kernel
	 */


	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @param  \Illuminate\Log\Writer  $log
	 * @return void
	 */
	protected function configureSingleHandler(Application $app, Writer $log)
	{
		$log->useFiles($app->storagePath().'/logs/laravel-'. ($app->runningInConsole() ? 'cli' : 'www') .'.log', config('app.log_level'));
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @param  \Illuminate\Log\Writer  $log
	 * @return void
	 */
	protected function configureDailyHandler(Application $app, Writer $log)
	{
		$log->useDailyFiles(
			$app->storagePath().'/logs/laravel-'. ($app->runningInConsole() ? 'cli' : 'www') .'.log',
			$app->make('config')->get('app.log_max_files', 5),
			config('app.log_level')
		);
	}

}