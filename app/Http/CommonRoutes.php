<?php


//$commonRoutes = function() {

	Route::get('/refresh', 'HomeController@refreshSession');
	Route::get('auth/getUser', 'Auth\AuthController@getUser');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/signup', 'Auth\AuthController@postSignup');
	Route::get('auth/signup/{referer_id?}', 'Auth\AuthController@getSignup');
	Route::post('auth/logout', 'Auth\AuthController@postLogout');
	Route::get('auth/activate/{confirmation_code}', 'Auth\AuthController@getActivate');
	Route::get('auth/reset', 'Auth\AuthController@getPasswordReset');
	Route::post('auth/reset', 'Auth\AuthController@postPasswordReset');
	Route::get('auth/new_password/{reset_token}', 'Auth\AuthController@getNewPassword');
	Route::post('auth/new_password', 'Auth\AuthController@postNewPassword');
	Route::get('auth/notification', 'Auth\AuthController@showNotification');
	Route::controllers([
			'fiware' => 'Auth\FIWareController'
	]);

	Route::get('sitemap', function() {

		return redirect()->action('HomeController@sitemap', [], 302);
	});

	Route::get('sitemap_index.xml', function() {

		return redirect()->action('HomeController@sitemap', [], 302);
	});
	Route::get('sitemap.xml', 'HomeController@sitemap');

//};
