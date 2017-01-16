<?php


//$adminRoutes = function() {


	Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {

		Route::get('donation/{user_id?}', 'Admin\DonationController@index');
		Route::post('donation/load/{user_id?}', 'Admin\DonationController@loadDonations');

		Route::get('seo', 'StaticController@seo');
		Route::get('sync', 'Admin\SyncController@index');
		Route::post('sync-media', 'Admin\SyncController@syncMedia');
		Route::get('sync-media-lock', 'Admin\SyncController@checkSyncMediaLock');

		Route::controller('/resources', 'ResourceController');

		Route::get('affiliate/{user_id?}', 'Admin\AffiliateController@showTransactions');
		Route::post('affiliate/load/{user_id?}', 'Admin\AffiliateController@loadTransactions');
		Route::post('affiliate/overridestatus/{id}/{status_override}', 'Admin\AffiliateController@updateStatusOverride');

		Route::get('user', 'Admin\UserController@showUsers');
		Route::post('user/load', 'Admin\UserController@loadUsers');
		Route::get('user/{user}', 'Admin\UserController@showSingleUser');
		Route::post('user/{user}/setDisabled/{disabled}', 'Admin\UserController@setDisabled');
		Route::delete('user/{user}', 'Admin\UserController@deleteUser');
		Route::post('user/{user}', 'Admin\UserController@sendConfirmationReminder');
		Route::post('user/{user}/setAdmin/{admin}', 'Admin\UserController@setAdmin');
		Route::post('user/{user}/resetPassword', 'Admin\UserController@resetPassword');
		Route::post('user/{user}/creditBonets', 'Admin\UserController@creditBonets');


		Route::group(['prefix' => 'account'], function() {

			Route::get('{user_id}', 'AccountController@indexAdmin');
			Route::post('{user_id}/history', 'AccountController@fetchHistoryAdmin');
			Route::post('updateInfo/{user_id}', 'AccountController@updateInfoAdmin');
		});

		Route::group(['prefix' => 'shop'], function() {

			Route::get('hidden', 'ShopController@indexInvisible');
			Route::post('fetchInvisible', 'ShopController@fetchInvisible');
			Route::post('deleteVoucher/{shop_id}', 'ShopController@deleteVoucher');
			Route::post('addVouchers/{shop_id}', 'ShopController@addVouchers');
			Route::post('visible/{shop_id}', 'ShopController@setVisibility');
			Route::delete('destroy/{shop_id}', 'ShopController@destroy');
			Route::get('create', 'ShopController@create');
			Route::post('store', 'ShopController@store');
			Route::put('update/{shop_id}', 'ShopController@update');
			Route::get('edit/{shop_id}', ['as' => 'shop.edit', 'uses' => 'ShopController@edit']);
		});

		Route::group(['prefix' => 'redeem'], function() {

			Route::post('fetchInvisible', 'RedeemController@fetchInvisible');
			Route::post('visible/{donation_id}', 'RedeemController@setVisibility');
			Route::get('hidden', 'RedeemController@indexInvisible');
			Route::post('visible/{donation_id}', 'RedeemController@setVisibility');
			Route::delete('destroy/{donation_id}', 'RedeemController@destroy');
			Route::get('create', 'RedeemController@create');
			Route::post('store', 'RedeemController@store');
			Route::put('update/{donation_id}', 'RedeemController@update');
			Route::get('edit/{donation_id}', ['as' => 'donation.edit', 'uses' => 'RedeemController@edit']);
		});


		Route::group(['prefix' => 'article'], function() {

			Route::get('hidden', 'ArticleController@indexInvisible');
			Route::post('visible/{article_id}', 'ArticleController@setVisibility');
			Route::get('hidden/{article_id}', 'ArticleController@showInvisible');
			Route::post('fetchInvisible', 'ArticleController@fetchInvisible');
			Route::delete('destroy/{article_id}', 'ArticleController@destroy');
			Route::get('create', 'ArticleController@create');
			Route::post('store', 'ArticleController@store');
			Route::put('update/{article_id}', 'ArticleController@update');
			Route::get('edit/{article_id}', ['as' => 'article.edit', 'uses' => 'ArticleController@edit']);

		});
	});
//};
