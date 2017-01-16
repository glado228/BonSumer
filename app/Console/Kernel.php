<?php namespace Bonsum\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'Bonsum\Console\Commands\Inspire',
		'Bonsum\Console\Commands\ImportShops',
		'Bonsum\Console\Commands\ImportVouchers',
		'Bonsum\Console\Commands\ImportUsers',
		'Bonsum\Console\Commands\ImportPointsChange',
		'Bonsum\Console\Commands\FetchTransactions',
		'Bonsum\Console\Commands\UpdateBonets',
		'Bonsum\Console\Commands\AddArticlesSeoTitle',
		'Bonsum\Console\Commands\SendReminders'
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('update:transactions --days=100 --save --bonets')->hourly();
		$schedule->command('update:transactions --days=365 --save')->dailyAt('1:10');
		$schedule->command('send:reminders')->hourly();
	}

	/**
	 * The bootstrap classes for the application.
	 *
	 * @var array
	 */
	protected $bootstrappers = [
		'Illuminate\Foundation\Bootstrap\DetectEnvironment',
		'Illuminate\Foundation\Bootstrap\LoadConfiguration',
		'Bonsum\Bootstrap\ConfigureLogging',  // our own logging configurator
		'Illuminate\Foundation\Bootstrap\HandleExceptions',
		'Illuminate\Foundation\Bootstrap\RegisterFacades',
		'Illuminate\Foundation\Bootstrap\SetRequestForConsole',
		'Illuminate\Foundation\Bootstrap\RegisterProviders',
		'Illuminate\Foundation\Bootstrap\BootProviders',
	];
}
