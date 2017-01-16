<?php namespace Bonsum\Exceptions;

use Bonsum\User;

class NotEnoughBonetsException extends UserException {


	public function __construct(User $user, $bonets) {

		parent::__construct($user, 'user '.$user->id.' cannot donate/redeem the requested ' . $bonets .' bonets');
	}
}
