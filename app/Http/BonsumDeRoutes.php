<?php

//$bonsumDeRoutes = function() {


	Route::get('/refresh', 'HomeController@refreshSession');

	Route::get('/der-bonsum-wald', 'StaticController@forest');

	Route::get('/howto', 'StaticController@howto');
	Route::get('/ueber-uns', 'StaticController@about');

	Route::get('/botschafter', 'StaticController@ambassador');

	Route::get('/nachhaltigkeitslexikon-siegel', 'LexiconController@index');
	Route::get('/faq', 'StaticController@faq');
	Route::get('/shop-betreiber', 'StaticController@shopOwners');

	Route::get('/presse', 'StaticController@press');
	Route::get('/kontakt', 'StaticController@contact');
	Route::get('/jetzt-mitmachen', 'StaticController@join');
	Route::get('/jobs_bonsum', function() {

		return redirect()->action('StaticController@jobs', [], 301);
	});
	Route::get('/jobs-bonsum', 'StaticController@jobs');

	Route::get('/impressum', 'StaticController@imprint');
	Route::get('/agb', 'StaticController@terms');
	Route::get('/datenschutzbestimmungen', 'StaticController@privacy');

	Route::get('/bonets-spenden', 'StaticController@donateBonets');
	Route::get('/bonets-in-gutscheine-einloesen', 'StaticController@redeemVouchers');

	Route::group(['prefix' => 'magazin'], function() {

		Route::post('fetch', 'ArticleController@fetch');
		Route::get('', 'ArticleController@index');
		Route::get('{article_id}', 'ArticleController@show');
	});

	Route::group(['prefix' => 'shops'], function() {

		Route::post('fetch', 'ShopController@fetch');
		Route::get('redirect/{shop_id}', 'ShopController@redirect');
		Route::get('', 'ShopController@index');
	});

	Route::group(['prefix' => 'bonets-einloesen'], function() {

		Route::post('fetch', 'RedeemController@fetch');
		Route::post('getVoucher/{shop_id}', 'RedeemController@getVoucher');
		Route::post('donate/{donation_id}', 'RedeemController@donate');
		Route::get('', 'RedeemController@index');
	});

	Route::get('voucher_types', function() {
		return redirect()->action('RedeemController@index', [], 301);
	});

	Route::group(['prefix' => 'konto'], function() {

		Route::get('/', 'AccountController@index');
		Route::post('updateInfo', 'AccountController@updateInfo');
		Route::post('updatePassword', 'AccountController@updatePassword');
		Route::post('history', 'AccountController@fetchHistory');
		Route::post('sendInviteEmail', 'AccountController@sendInviteEmail');
	});

	Route::get('/{article_title?}', 'HomeController@index');
//};
