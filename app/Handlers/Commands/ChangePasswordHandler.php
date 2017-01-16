<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\ChangePassword;
use Bonsum\Services\Registrar;
use Bonsum\Events\UserPasswordChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Guard;

class ChangePasswordHandler {

	protected $registrar;

	protected $auth;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Registrar $registrar, Guard $auth)
	{
		$this->registrar = $registrar;
		$this->auth = $auth;
	}

	/**
	 * Handle the command.
	 *
	 * @param  ChangePassword  $command
	 * @return void
	 */
	public function handle(ChangePassword $command)
	{
		$this->registrar->setPassword($command->user, $command->new_password);
		$command->user->reset_token = NULL;
		$command->user->reset_token_creation = NULL;
		$command->user->old_password = NULL;
		$command->user->save();
		$this->auth->login($command->user);

		event(new UserPasswordChanged($command->user));
	}

}
