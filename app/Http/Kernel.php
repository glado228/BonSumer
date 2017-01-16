<?php namespace Bonsum\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Bonsum\Http\Middleware\LogDBQueries',
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'Bonsum\Http\Middleware\VerifyCsrfToken',
		'Bonsum\Http\Middleware\LogOutDisabledUsers',
		'Bonsum\Http\Middleware\SetLocale',
		'Bonsum\Http\Middleware\NormalUserMode',
	    //'Clockwork\Support\Laravel\ClockworkMiddleware'
		// No traps: they cause problem with browser plugins that automatically fill forms
		//'Bonsum\Http\Middleware\Traps'
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'Bonsum\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'Bonsum\Http\Middleware\RedirectIfAuthenticated',
		'admin' => 'Bonsum\Http\Middleware\Admin'
	];

	/**
	 * The bootstrap classes for the application.
	 *
	 * @var array
	 */
	protected $bootstrappers = [
		'Illuminate\Foundation\Bootstrap\DetectEnvironment',
		'Illuminate\Foundation\Bootstrap\LoadConfiguration',
		'Bonsum\Bootstrap\ConfigureLogging', // our own logging configurator
		'Illuminate\Foundation\Bootstrap\HandleExceptions',
		'Illuminate\Foundation\Bootstrap\RegisterFacades',
		'Illuminate\Foundation\Bootstrap\RegisterProviders',
		'Illuminate\Foundation\Bootstrap\BootProviders',
	];

}
