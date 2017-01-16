<?php namespace Bonsum\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Bonsum\MerchantTransaction;
use Bonsum\User;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'Bonsum\Events\FailedLogin' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\OldPasswordImported' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\MerchantTransactionsDownload' => [
			'Bonsum\Handlers\Events\BaseEventHandler'
		],
		'Bonsum\Events\UserCreated' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserConfirmed' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserConfirmationCodeSent' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserConfirmationReminderSent' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserPasswordReset' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserPasswordChanged' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserDisabled' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserActivated' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserDeleted' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserPersonalDataChanged' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserAdminRightsGranted' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\UserAdminRightsRevoked' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\BonetsUpdateNotCommitted' => [
			'Bonsum\Handlers\Events\BaseEventHandler'
		],
		'Bonsum\Events\BonetsComputed' => [
			'Bonsum\Handlers\Events\BaseEventHandler'
		],
		'Bonsum\Events\BonetsCredited' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\BonetsDonated' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		],
		'Bonsum\Events\BonetsRedeemed' => [
			'Bonsum\Handlers\Events\BaseEventHandler',
		]
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		MerchantTransaction::saving(function($transaction) {
			// Unset the _rawData field before saving
			// this field contains the raw data received from the affiliate network, and it's used
			// for debugging and inspection
			unset($transaction->_rawData);
		});

		User::saving(function($user) {
			unset($user->normal_user_mode);
		});
	}

}
