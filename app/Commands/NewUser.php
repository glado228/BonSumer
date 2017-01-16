<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;

class NewUser extends Command {


	public $firstname;

	public $lastname;

	public $gender;

	public $mail;

	public $password;

	public $send_activation_email;

	public $admin;

	public $preferred_locale;

	// user ID who refered this user (if any)
	public $referer_id;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct($firstname, $lastname, $gender, $email, $password, $send_activation_email, $admin, $preferred_locale, $referer_id)
	{
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->gender = $gender;
		$this->email = $email;
		$this->password = $password;
		$this->send_activation_email = $send_activation_email;
		$this->admin = $admin;
		$this->preferred_locale = $preferred_locale;
		$this->referer_id = $referer_id;
	}

}
