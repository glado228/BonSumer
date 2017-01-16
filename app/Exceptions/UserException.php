<?php namespace Bonsum\Exceptions;

use RuntimeException;

class UserException extends RuntimeException {


	/**
	 * The user model or user id
	 * @var Bonsum\User|int
	 */
	protected $user;


	public function __construct($user, $msg) {

		parent::__construct($msg);
		$this->user = $user;
	}

}
