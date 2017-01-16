<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => env('APP_DEBUG'),

	/*
	 * When this option is turned on and debug mode is on, AJAX requests
	 * will receive full HTML error descriptions in case of errors. Otherwise,
	 * A JSON summary of the error will be sent back to the client
	 */

	'ajax_debug' => env('APP_AJAX_DEBUG'),

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/

	'url' => 'http://localhost',

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => 'en-UK',

	'available_locales' => ['en-UK', 'de-DE', 'de-CH', 'de-AT'], // unsupported for now: 'en-US'

	/*
		this array specifies flexible fallback locales. For example an entry like:

			'de-CH' => 'de-DE'

		means that the fallback locale for de-CH is de-DE. (i.e. if a translation is not found in de-CH
		it will be searched in de-DE)

		If a locale does not appear in this array, the global fallback locale will be used.
	*/
	'fallback_locales' => [
		'de-CH' => 'de-DE',
		'de-AT' => 'de-DE'
	],

	'domain_to_locale' => [
		'uk' => 'en-UK',
		'de' => 'de-DE',
		'ch' => 'de-CH',
		'at' => 'de-AT',
		'com' => 'en-US',
		'net' => 'de-DE'
	],

	/*
	|--------------------------------------------------------------------------
	| Application Fallback Locale
	|--------------------------------------------------------------------------
	|
	| The fallback locale determines the locale to use when the current one
	| is not available. You may change the value to correspond to any of
	| the language folders that are provided through your application.
	|
	*/

	'fallback_locale' => 'en-UK',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/

	'key' => env('APP_KEY', 'SomeRandomString'),

	'cipher' => MCRYPT_RIJNDAEL_256,

	/*
	|--------------------------------------------------------------------------
	| Logging Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log settings for your application. Out of
	| the box, Laravel uses the Monolog PHP logging library. This gives
	| you a variety of powerful log handlers / formatters to utilize.
	|
	| Available Settings: "single", "daily", "syslog", "errorlog"
	|
	*/

	'log' => env('APP_LOG_HANDLER', 'single'),

	/*
		For the daily logger, the maxmimum number of files to keep
	 */
	'log_max_files' => env('APP_LOG_MAX_FILES', 5),

	/*
		Minimum log level
	 */
	'log_level' => env('APP_LOG_LEVEL', 'debug'),

	/*
		Command uset to synchronize the media files
	 */
	'sync_media_cmd' => env('SYNC_MEDIA_CMD'),

	/*
	|--------------------------------------------------------------------------
	| Autoloaded Service Providers
	|--------------------------------------------------------------------------
	|
	| The service providers listed here will be automatically loaded on the
	| request to your application. Feel free to add your own services to
	| this array to grant expanded functionality to your applications.
	|
	*/

	'providers' => [

		/*
		 * Laravel Framework Service Providers...
		 */
		'Illuminate\Foundation\Providers\ArtisanServiceProvider',
		'Illuminate\Auth\AuthServiceProvider',
		'Illuminate\Bus\BusServiceProvider',
		'Illuminate\Cache\CacheServiceProvider',
		'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
		'Illuminate\Routing\ControllerServiceProvider',
		'Illuminate\Cookie\CookieServiceProvider',
		'Illuminate\Database\DatabaseServiceProvider',
		'Illuminate\Encryption\EncryptionServiceProvider',
		'Illuminate\Filesystem\FilesystemServiceProvider',
		'Illuminate\Foundation\Providers\FoundationServiceProvider',
		'Illuminate\Hashing\HashServiceProvider',
		'Illuminate\Mail\MailServiceProvider',
		'Illuminate\Pagination\PaginationServiceProvider',
		'Illuminate\Pipeline\PipelineServiceProvider',
		'Illuminate\Queue\QueueServiceProvider',
		'Illuminate\Redis\RedisServiceProvider',
		'Illuminate\Auth\Passwords\PasswordResetServiceProvider',
		'Illuminate\Session\SessionServiceProvider',
		'Illuminate\Translation\TranslationServiceProvider',
		'Illuminate\Validation\ValidationServiceProvider',
		'Illuminate\View\ViewServiceProvider',
		'Illuminate\Html\HtmlServiceProvider',
	    'Laracasts\Utilities\JavaScript\JavascriptServiceProvider',
	    'Laravel\Socialite\SocialiteServiceProvider',
		'Jenssegers\Date\DateServiceProvider',
		'Jenssegers\Mongodb\MongodbServiceProvider',
//	    'Clockwork\Support\Laravel\ClockworkServiceProvider',
	    'Roumen\Sitemap\SitemapServiceProvider',

		/*
		 * Application Service Providers...
		 */
		'Bonsum\Providers\AppServiceProvider',
		'Bonsum\Providers\BusServiceProvider',
		'Bonsum\Providers\ConfigServiceProvider',
		'Bonsum\Providers\EventServiceProvider',
		'Bonsum\Providers\RouteServiceProvider',
        'Bonsum\Providers\FrontEndServiceProvider',
        'Bonsum\Providers\ResourceServiceProvider',
        'Bonsum\Providers\FIWareUserServiceProvider',
        'Bonsum\Providers\MerchantTransactionsServiceProvider',
        'Bonsum\Providers\BonetsServiceProvider',
        'Bonsum\Providers\ShopServiceProvider',
        'Bonsum\Providers\DonationOptionServiceProvider',
        'Bonsum\Providers\ArticleServiceProvider',
        'Bonsum\Providers\SeoServiceProvider',
        'Bonsum\Providers\LocalizationServiceProvider'


	],

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are "lazy" loaded so they don't hinder performance.
	|
	*/

	'aliases' => [

		'App'       => 'Illuminate\Support\Facades\App',
		'Artisan'   => 'Illuminate\Support\Facades\Artisan',
		'Auth'      => 'Illuminate\Support\Facades\Auth',
		'Blade'     => 'Illuminate\Support\Facades\Blade',
		'Bus'       => 'Illuminate\Support\Facades\Bus',
		'Cache'     => 'Illuminate\Support\Facades\Cache',
		'Config'    => 'Illuminate\Support\Facades\Config',
		'Cookie'    => 'Illuminate\Support\Facades\Cookie',
		'Crypt'     => 'Illuminate\Support\Facades\Crypt',
		'DB'        => 'Illuminate\Support\Facades\DB',
		'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
		'Event'     => 'Illuminate\Support\Facades\Event',
		'File'      => 'Illuminate\Support\Facades\File',
		'Hash'      => 'Illuminate\Support\Facades\Hash',
		'Input'     => 'Illuminate\Support\Facades\Input',
		'Inspiring' => 'Illuminate\Foundation\Inspiring',
		'Lang'      => 'Illuminate\Support\Facades\Lang',
		'Log'       => 'Illuminate\Support\Facades\Log',
		'Mail'      => 'Illuminate\Support\Facades\Mail',
		'Password'  => 'Illuminate\Support\Facades\Password',
		'Queue'     => 'Illuminate\Support\Facades\Queue',
		'Redirect'  => 'Illuminate\Support\Facades\Redirect',
		'Redis'     => 'Illuminate\Support\Facades\Redis',
		'Request'   => 'Illuminate\Support\Facades\Request',
		'Response'  => 'Illuminate\Support\Facades\Response',
		'Route'     => 'Illuminate\Support\Facades\Route',
		'Schema'    => 'Illuminate\Support\Facades\Schema',
		'Session'   => 'Illuminate\Support\Facades\Session',
		'Storage'   => 'Illuminate\Support\Facades\Storage',
		'URL'       => 'Illuminate\Support\Facades\URL',
		'Validator' => 'Illuminate\Support\Facades\Validator',
		'View'      => 'Illuminate\Support\Facades\View',
		'HTML'      => 'Illuminate\Html\HtmlFacade',
        'Form'      => 'Illuminate\Html\FormFacade',
        'Socialite' => 'Laravel\Socialite\Facades\Socialite'
	],

];
