<?php namespace Bonsum\Events;

use Request;
use ReflectionClass;

abstract class Event {

	public function __construct() {

		$this->ip = Request::getClientIp();
	}


	public function __toString() {

		$rc = new ReflectionClass(get_class($this));

		return $rc->getShortName() . ': ' . json_encode(get_object_vars($this));
	}

}
