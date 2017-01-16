<?php namespace Bonsum\Handlers\Events;


class BaseEventHandler {


	public function handle($event) {

		info($event);
	}
}
