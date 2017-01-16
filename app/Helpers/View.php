<?php namespace Bonsum\Helpers;

use App;
use View as ViewFacade;

class View {

	/**
	 * Get the localized view name for the given view, or falls back to a non-localized view
	 * @param  string $view_name
	 * @return string $locale (optional)
	 */
	static public function getLocalizedView($view_name, $locale = NULL) {

		return App::make('localization')->getLocalizedView($view_name, $locale, null, true);
	}
}
