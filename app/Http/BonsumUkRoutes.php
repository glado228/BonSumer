<?php

//$defaultRoutes = function() {

	Route::get('/the-bonsum-forest', 'StaticController@forest');

	Route::get('/howto', 'StaticController@howto');
	Route::get('/about-us', 'StaticController@about');

	Route::get('/ambassadors', 'StaticController@ambassador');

	Route::get('/lexicon', 'LexiconController@index');
	Route::get('/faq', 'StaticController@faq');
	Route::get('/shop_owners', 'StaticController@shopOwners');

	Route::get('/press', 'StaticController@press');
	Route::get('/contact', 'StaticController@contact');
	Route::get('/join', 'StaticController@join');
	Route::get('/jobs-bonsum', 'StaticController@jobs');

	Route::get('/imprint', 'StaticController@imprint');
	Route::get('/terms', 'StaticController@terms');
	Route::get('/privacy', 'StaticController@privacy');

	Route::get('/donate-bonets', 'StaticController@donateBonets');
	Route::get('/redeem-vouchers', 'StaticController@redeemVouchers');

	Route::group(['prefix' => 'magazine'], function() {

		Route::get('{article_id}', 'ArticleController@show');
		Route::post('fetch', 'ArticleController@fetch');
		Route::get('', 'ArticleController@index');
	});

	Route::group(['prefix' => 'shops'], function() {

		Route::post('fetch', 'ShopController@fetch');
		Route::get('redirect/{shop_id}', 'ShopController@redirect');
		Route::get('', 'ShopController@index');
	});

	Route::group(['prefix' => 'redeem'], function() {

		Route::post('fetch', 'RedeemController@fetch');
		Route::post('getVoucher/{shop_id}', 'RedeemController@getVoucher');
		Route::post('donate/{donation_id}', 'RedeemController@donate');
		Route::get('', 'RedeemController@index');
	});

	Route::group(['prefix' => 'account'], function() {

		Route::get('/', 'AccountController@index');
		Route::post('updateInfo', 'AccountController@updateInfo');
		Route::post('updatePassword', 'AccountController@updatePassword');
		Route::post('history', 'AccountController@fetchHistory');
		Route::post('sendInviteEmail', 'AccountController@sendInviteEmail');
	});

	Route::get('/{article_title?}', 'HomeController@index');
//};

