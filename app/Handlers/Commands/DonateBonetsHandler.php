<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\DonateBonets;
use DB;
use Bonsum\Events\BonetsDonated;
use Bonsum\Exceptions\UserException;
use Bonsum\Helpers\Mail as MailHelper;
use Bonsum\BonetsDonation;
use Bonsum\Exceptions\NotEnoughBonetsException;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Bonsum\Services\Bonets;
use Bonsum\User;
use Bonsum\Services\Localization;

class DonateBonetsHandler {

	/**
	 * Bonsum\Services\Bonets
	 * @var [type]
	 */
	protected $bonets_service;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Bonets $bonets_service, Localization $localization)
	{
		$this->bonets_service = $bonets_service;
		$this->localization = $localization;
	}

	/**
	 * Handle the command.
	 *
	 * @param  DonateBonets  $command
	 * @return void
	 */
	public function handle(DonateBonets $command)
	{
		$bonets_service = $this->bonets_service;
		list($user, $donation) = DB::transaction(function() use ($command, $bonets_service) {

			$user = User::where('id', '=', $command->user_id)->lockForUpdate()->first();
			if (!$user) {
				throw new UserException($command->user_id, 'The user could not be found');
			}

			$user->decrement('bonets', $command->bonets);

			$donation = BonetsDonation::create([
				'date' => new Carbon,
				'user_id' => $user->id,
				'donation_id' => $command->receiver->id,
				'bonets' => $command->bonets,
				'amount' => $bonets_service->fromBonets($command->bonets),
				'currency' => $bonets_service->getCurrency()
			]);

			return [$user, $donation];
		});

		event(new BonetsDonated($user, $donation));

		MailHelper::mailUser($user, 'donation', 'redeem.donation_subject', [
			'bonets' => $command->bonets,
			'amount' => $donation->amount,
			'currency' => $donation->currency,
			'receiver' => $command->receiver->name
		]);

	}

}
