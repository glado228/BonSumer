<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\UpdateBonets as UpdateBonetsCommand;
use Bonsum\Services\Bonets;
use Bonsum\User;
use Bonsum\Events\BonetsComputed;
use Illuminate\Queue\InteractsWithQueue;

class UpdateBonetsHandler {

	/**
	 * Bonsum\Services\Bonets
	 * @var [type]
	 */
	protected $bonets;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Bonets $bonets)
	{
		$this->bonets = $bonets;
	}

	/**
	 * Handle the command.
	 *
	 * @param  BonsumCommandUpdateBonets  $command
	 * @return void
	 */
	public function handle(UpdateBonetsCommand $command)
	{
		$start_time = microtime(TRUE);

		$total_bonets = 0;

		User::chunk(10000, function($users) use (&$total_bonets) {

			foreach ($users as $user) {
				$total_bonets += $this->bonets->updateUserBonets($user);
			}
		});

		$total_time = microtime(TRUE) - $start_time;

		event(new BonetsComputed($total_time, $total_bonets));
	}

}
