<?php namespace Bonsum\Affiliate;


interface Redirector {

	/**
	 * Extends the affiliate network link with SubID informations
	 * used to track user and shop
	 * @param  string $network_link the original affiliate network link
	 * @param  string $shop_id      the shop id
	 * @param  string $user_id      the user id
	 * @return string               link with SubID information
	 */
	public static function makeSubIDLink($network_link, $shop_id, $user_id);
}
