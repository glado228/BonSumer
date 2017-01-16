<?php namespace Bonsum\Events;

use Bonsum\User;

abstract class UserEvent extends Event {

	public $user;

	public function __construct(User $user) {

		parent::__construct();
		$this->user = $user;
	}
}
