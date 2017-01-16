<?php namespace Bonsum\Helpers;


class Url {


	/**
	 * build a URL from the result of parse_url
	 * @param  [type] $parse_results [description]
	 * @return [type]                [description]
	 */
	static public function build($parse_results) {

		return array_get($parse_results, 'scheme', 'http')
		. '://'
		. array_get($parse_results, 'host')
		. array_get($parse_results, 'path')
		. (!empty($parse_results['query']) ? '?'. $parse_results['query'] : '');
	}

	/**
	 * Given a title, make a url-usable version by removing capitalization and combining words with '-'
	 * @param  [type] $title [description]
	 * @return [type]        [description]
	 */
	static public function makeUrlFriendlyString($title) {

		$title = str_replace("\xc3\xa4", 'ae', $title);
		$title = str_replace("\xc3\x84", 'Ae', $title);

		$title = str_replace("\xc3\xb6", 'oe', $title);
		$title = str_replace("\xc3\x96", 'Oe', $title);

		$title = str_replace("\xc3\xbc", 'ue', $title);
		$title = str_replace("\xc3\x9c", 'Ue', $title);
		$title = str_replace("\xc3\x9f", 'ss', $title);

		$title = preg_replace('/[^0-9a-zA-Z\s]+/', '', $title);

		return implode('-', array_map('strtolower', preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY)));
	}
}
