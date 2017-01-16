<?php namespace Bonsum\Http\Middleware;

use Session;
use App;
use Closure;
use Config;
use Bonsum\Services\Localization;
use Lang;

class SetLocale {


	/**
	 *	@var array
	 */
	protected $available_locales;


	protected $domain_to_locale;


	public function __construct(Localization $localization) {

		$this->localization = $localization;
		$this->available_locales = Config::get('app.available_locales');
		$this->domain_to_locale = Config::get('app.domain_to_locale');
	}


	/**
	 * Sets the application locale by looking at (in oder):
	 *
	 * 1) setLocale input parameter
	 * 2) 'locale' key in session
	 * 3) domain name of server
	 * 4) Accept language header
	 *
	 *
	 * @param  [type]  $request [description]
	 * @param  Closure $next    [description]
	 * @return [type]           [description]
	 */
	public function handle($request, Closure $next) {


		$locale = NULL;
		$from_session = false;

		if ($request->has('setLocale')) {
			// user wants to explicitly set the locale. We also update the session
			$input_locale = $request->input('setLocale');
			if (in_array($input_locale, $this->available_locales)) {
				$locale = $input_locale;
				Session::set('locale', $locale);
			}

		}

		if (!$locale) {

			if (Session::has('locale')) {
				// locale set in seession
				$locale = Session::get('locale');
				$from_session = true;

			} else {
				// try the domain name
				$host = $request->getHost();
				if (($lastdot = strrpos($host, '.')) !== FALSE) {
					$top_level_domain = substr($host, $lastdot+1);
					$mapped_locale = array_get($this->domain_to_locale, $top_level_domain);
					if (in_array($mapped_locale, $this->available_locales)) {
						$locale = $mapped_locale;
					}
				}
			}
		}

		if ($locale) {
			App::setLocale($locale);
			Lang::setFallback($this->localization->getFallbackLocale($locale));
			/*
				\Jenssegers\Date\Date uses by default the Laravel translator.
				This is bad because it works with simple language IDs (e.g. 'en', 'de') while
				our translator works with country-based localization (e.g. en-UK)
				so we set the translator to NULL and force \Jenssegers\Date\Date to create its own local copy
			 */
			\Jenssegers\Date\Date::setTranslator(NULL);
			\Jenssegers\Date\Date::setLocale($this->localization->getLang($locale));
		}

		// make locale and language available to frontend
		app('frontend')->addVars([
			'locale' => App::getLocale(),
			'language' => $this->localization->getLang()
		]);

		return $next($request);
	}

}
