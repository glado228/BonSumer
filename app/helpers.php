<?php

use Bonsum\Helpers\Resource;
use Bonsum\Services\Resource as ResourceService;
use Bonsum\Services\Localization as Localization;

if (!function_exists('LR')) {

	/*
	 * Wrapper for trans() that creates a text resource editable in-place
	 */
	function LR($id = null, $parameters = array(), $domain = 'messages', $locale = null) {

		return Resource::LR($id, $parameters, $domain, $locale);
	}
}

if (!function_exists('MEDIA')) {

	function MEDIA($path, $type = ResourceService::RESOURCE_TYPE_IMG, $secure = null, $attributes = []) {

		return Resource::MEDIA($path, $type, $secure, $attributes);
	}
}


if (!function_exists('IMG')) {

	function IMG($path, $secure = null, $attributes = []) {

		return MEDIA($path, ResourceService::RESOURCE_TYPE_IMG, $secure, $attributes);
	}
}


if (!function_exists('parse_tags')) {

	/**
	 * parse a comma separated list of tags and converts them to the following format:
	 *
	 *	[
	 *		[
	 *			'text' => 'tag1'
	 *		],
	 *		[
	 *			'text' => 'tag2'
	 *		]
	 *	]
	 *
	 * @param  string $raw_tags comma separated list of tags
	 * @return array
	 */
	function parse_tags($raw_tags) {

		return array_map(function($tag) {
			return [
				'text' => trim($tag)
			];
		},
		array_filter(explode(',', $raw_tags), function($tag) {
			return $tag !== '';
		})
		);
	}
}

if (!function_exists('locale_name')) {

	/**
	 * Returns the name used to represent a locale (in menus and such)
	 *
	 * @param  string|null    $locale locale descriptor or null to use current locale
	 * @return string         locale specific name
	 */
	function locale_name($locale=null) {
		if(!$locale) {
			$locale = App::getLocale();
		}

		switch ($locale) {
			case 'de-DE':
				return 'Germany';
			case 'en-UK':
				return 'UK';
			case 'en-US':
				return 'USA';
			case 'de-CH':
				return 'Switzerland';
			case 'de-AT':
				return 'Austria';
		}
		return 'UK';
	}
}
