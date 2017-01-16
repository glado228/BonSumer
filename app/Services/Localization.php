<?php namespace Bonsum\Services;

use App;
use Config;
use Lang;
use Bonsum\User;
use View;
use Crypt;

class Localization {

	/**
	 * Needs to be changed when we switch to https
	 */
	const SCHEMA = 'http://';


	/**
	 * Array of fallback locales
	 * @var [type]
	 */
	protected $fallback_locales;


	public function __construct() {

		$this->fallback_locales = Config::get('app.fallback_locales');
	}


	/**
	 * return a language from a locale descriptor
	 *
	 * for example, both en-US and en-UK returns en
	 *
	 * @param  string|null    $locale locale descriptor or null to use current locale
	 * @return string         language
	 */
	public function getLang($locale = null) {

		return substr($locale ?: App::getLocale(), 0, 2);
	}

	/**
	 * get the fallback locale for a specific locale
	 * @param  [type] $locale [description]
	 * @param  $only_explicit return a fallback locale only if it is specifically defined in $this->fallback_locales
	 * @return [type]         [description]
	 */
	public function getFallbackLocale($locale = null, $only_explicit = false) {

		if (!$locale) {
			$locale = App::getLocale();
		}

		if (!empty($this->fallback_locales[$locale])) {
			return $this->fallback_locales[$locale];
		}

		return ($only_explicit ? NULL : Lang::getFallback());
	}

	/**
	 * get the hostname for a specific locale
	 * for examlpe, de-DE returns bonsum.de and en-UK returns bonsum.co.uk
	 * @param  string|null    $locale locale descriptor or null to use current locale
	 * @return string         language
	 */
	public function getHostName($locale = null) {

		if (!$locale) {
			$locale = App::getLocale();
		}

		if (strlen($locale) > 2) {
			$locale = str_replace('_', '-', $locale);
			$separator = strpos($locale, '-');
			if ($separator === FALSE) {
				$ccode = substr($locale, 0, 2);
			} else {
				$ccode = substr($locale, $separator+1, 2);
			}

		} else {
			$ccode = $locale;
		}

		$ccode = strtolower($ccode);

		switch ($ccode) {

			case 'de':
				return 'bonsum.de';
			case 'uk':
				return 'bonsum.co.uk';
			case 'us':
				return 'bonsum.com';
			case 'ch':
				return 'bonsum.ch';
			case 'at':
				return 'bonsum.at';
		}

		return 'bonsum.co.uk';
	}

	/**
	 * return a view for a given locale
	 * @param  [type] $locale [description]
	 * @return [type]         [description]
	 */
	public function getLocalizedView($view_name, $locale = null, $prefix = null, $only_explicit_fallback = false) {

		if (!$locale) {
			$locale = App::getLocale();
		}

		if ($prefix) {
			$prefix .= '.';
		} else {
			$prefix = '';
		}

		$local_view_name = $prefix . $locale . '.' . $view_name;

		if (View::exists($local_view_name)) {
			return $local_view_name;
		}

		$fallback_locale = $this->getFallbackLocale($locale, $only_explicit_fallback);

		if ($fallback_locale) {
			$fallback_view_name = $prefix . $fallback_locale . '.' . $view_name;
			if (View::exists($fallback_view_name)) {
				return $fallback_view_name;
			}
		}

		return $prefix . $view_name;
	}


	/**
	 * Get a localized invite link
	 * @param  [Bonsum\User] $referer_id  user who will be  sending the invitation
	 * @param  [type] $locale     [description]
	 * @return [type]             [description]
	 */
	public function getInviteUrl(User $referer, $locale = null) {

		return self::SCHEMA. $this->getHostName($locale) . '/auth/signup/'. Crypt::encrypt($referer->id);
	}

	/**
	 * get a localized user accoung link
	 * @param  [type] $locale [description]
	 * @return [type]         [description]
	 */
	public function getUserAccountUrl($locale = null) {

		$language = $this->getLang($locale);

		$path = 'account';
		switch($language) {
			case 'de':
				$path = 'konto';
		}
		return self::SCHEMA . $this->getHostName($locale) . '/' . $path;
	}

	/**
	 * Returns a localized activation link
	 * @param  [type] $confirmation_code [description]
	 * @param  [type] $locale            [description]
	 * @return [type]                    [description]
	 */
	public function getActivationUrl($confirmation_code, $locale = null) {

		return self::SCHEMA . $this->getHostName($locale) . '/auth/activate/'. $confirmation_code;
	}

	/**
	 * Returns a localized password reset link
	 * @param  [type] $reset_token [description]
	 * @param  [type] $locale      [description]
	 * @return [type]              [description]
	 */
	public function getPasswordResetUrl($reset_token, $locale = null) {

		return self::SCHEMA . $this->getHostName($locale) . '/auth/new_password/'. $reset_token;
	}


	public function getEmailSender($locale = null) {

		return ['address' => 'info@'. $this->getHostName($locale), 'name' => 'Bonsum Team'];
	}
}
