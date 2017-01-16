<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// common routes
require app_path('Http/CommonRoutes.php');

// get host name without 'www.'
$host = str_ireplace('www.', '', Request::getHost());

// register localized routes based on host name
switch ($host) {

	case 'bonsum.de':
	case 'bonsum.at':
	case 'bonsum.ch':
		require app_path('Http/BonsumDeRoutes.php');
		break;

	case 'bonsum.co.uk':
		require app_path('Http/BonsumUkRoutes.php');
		break;

	default:
		require app_path('Http/BonsumUkRoutes.php');
}

if (App::environment() !== 'production') {
	// Admin routes, not available in production
	require app_path('Http/AdminRoutes.php');
}
