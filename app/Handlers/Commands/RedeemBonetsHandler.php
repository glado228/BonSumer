<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Events\BonetsRedeemed;
use Bonsum\Commands\RedeemBonets;
use Bonsum\Services\Bonets;
use Bonsum\BonetsRedeem;
use DB;
use Mail;
use Bonsum\Exceptions\NotEnoughBonetsException;

use Bonsum\User;
use Bonsum\Helpers\Mail as MailHelper;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Bonsum\Services\Localization;

class RedeemBonetsHandler {

	/**
	 *
	 * @var Bonsum\Services\Bonets
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
	 * @param  RedeemBonets  $command
	 * @return void
	 */
	public function handle(RedeemBonets $command)
	{
		$bonets_service = $this->bonets_service;
		list($user, $redeem) = DB::transaction(function() use ($command, $bonets_service) {

			$user = User::where('id', '=', $command->user_id)->lockForUpdate()->first();
			if (!$user) {
				throw new UserException($command->user_id, 'The user could not be found');
			}

			$bonets = $bonets_service->toBonets($command->amount);

			$user->decrement('bonets', $bonets);

			$redeem = BonetsRedeem::create([
				'date' => new Carbon,
				'user_id' => $user->id,
				'shop_id' => $command->shop->id,
				'amount' => $command->amount,
				'bonets' => $bonets,
				'currency' => $bonets_service->getCurrency(),
				'voucher_code' => $command->voucher_code
			]);

			return [$user, $redeem];
		});

		event(new BonetsRedeemed($user, $redeem));

		MailHelper::mailUser($user, 'redeem', 'redeem.voucher_subject', [
			'bonets' => $redeem->bonets,
			'amount' => $command->amount,
			'currency' => $redeem->currency,
			'shop' => $command->shop->name,
			'voucher_code' => $command->voucher_code,
		]);
	}

}
