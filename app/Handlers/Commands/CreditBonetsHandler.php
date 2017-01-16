<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\CreditBonets;
use Bonsum\User;
use Bonsum\BonetsCredit;
use Bonsum\Events\BonetsCredited;
use Bonsum\Exceptions\UserException;
use DB;
use Carbon\Carbon;
use Exception;

use Illuminate\Queue\InteractsWithQueue;
use Mail;
use Bonsum\Helpers\Mail as MailHelper;


class CreditBonetsHandler {


	protected $max_creditable_bonets;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->max_creditable_bonets = config('bonets.max_creditable_bonets', CreditBonets::DEFAULT_MAX_CREDITABLE);
	}

	/**
	 * Handle the command.
	 *
	 * @param  CreditBonets  $command
	 * @return void
	 */
	public function handle(CreditBonets $command)
	{
		if ($command->bonets > $this->max_creditable_bonets) {
			throw UserException($command->user_id, 'Cannot credit more than ' . $this->max_creditable_bonets);
		}

		list($user, $bonets_credit) = DB::transaction(function() use ($command) {

			$user = User::where('id', '=', $command->user_id)->lockForUpdate()->first();
			if (!$user) {
				throw new UserException($command->user_id, 'The user could not be found');
			}

			$user->increment('bonets', $command->bonets);

			$bonets_credit = BonetsCredit::create([
				'bonets' => $command->bonets,
				'user_id' => $command->user_id,
				'date' => new Carbon,
				'description' => $command->message
			]);

			return [$user, $bonets_credit];
		});

		event(new BonetsCredited($user, $bonets_credit));

	}

}
