<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Bonsum\User;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bonsum\Commands\SendConfirmationReminder;

class SendReminders extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'send:reminders';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send reminders that are due.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(\Illuminate\Contracts\Bus\Dispatcher $dispatcher)
	{
		User::where('confirmed', '=', false)
			->where('confirmation_reminder_sent', '=', false)
			->chunk(1000, function($users) use ($dispatcher) {

				foreach ($users as $user) {

					if ($user->confirmation_code_creation->diffInDays() >= 1) {

						$dispatcher->dispatch(new SendConfirmationReminder($user));
					}
				}
		});
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		];
	}

}
