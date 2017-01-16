<?php namespace Bonsum\Helpers;

use App;
use View;
use Bonsum\User;
use Mail as MailFacade;

class Mail {

	/**
	 * Get the localized view name for the given email view, or falls back to the default locale
	 * @param  string $mail_view_name
	 * @return string $locale (optional)
	 */
	static public function getLocalizedView($mail_view_name, $locale = NULL) {

		return App::make('localization')->getLocalizedView($mail_view_name, $locale, 'emails');
	}


	/**
	 * Send an email to an user using the right localized text
	 * @param  Bonsum\User    $user          The recipient
	 * @param  [type]  $view_template the name of the template to use
	 * @param  string|array  $subject   the language resource to use for the subject
	 *                                  Either a string or an array [language_resource, array of parameteres]
	 * @param  array  $data          array of data to inject in the view
	 * @param  string|null $locale    locale to use. If null, the preferred user's locale will be used
	 * @param  string|null $recipient here you can specifiy an alternative recipient. If null, the user's email will be used
	 * @param  boolean $queue         whether to queue the email or not (default = yes)
	 * @return void                 [description]
	 *
	 * Note: user's name, invite_url and account_url will automatically be injected in the view
	 * To avoid this, just set them to FALSE in your $data array
	 *
	 */
	static public function mailUser(User $user, $view_template, $subject, array $data, $locale = null, $recipient = null, $queue = true)
	{
		$locale = ($locale ?: $user->preferred_locale);
		$localization = app('localization');

		$from = $localization->getEmailSender($locale);

		$subject = (is_array($subject) ? $subject : [$subject, []]);
		list($subject_key, $subject_params) = $subject;

		$subject_text = trans($subject_key, $subject_params, 'messages', $locale);
		$to = ($recipient ?: $user->email);

		$callback = function($message) use ($from, $subject_text, $to) {
			$message->subject($subject_text);
			$message->from($from['address'], $from['name']);
			$message->to($to);
		};

		if (array_get($data, 'name') !== FALSE) {
			$data['name'] = $user->firstname;
		} else {
			unset($data['name']);
		}
		if (array_get($data, 'invite_url') !== FALSE) {
			$data['invite_url'] = $localization->getInviteUrl($user, $locale);
		} else {
			unset($data['invite_url']);
		}
		if (array_get($data, 'account_url') !== FALSE) {
			$data['account_url'] = $localization->getUserAccountUrl($locale);
		} else {
			unset($data['account_url']);
		}

		$view = [
			'text' => self::getLocalizedView($view_template, $locale)
		];

		if ($queue) {
			MailFacade::queue($view, $data, $callback);
		} else {
			MailFacade::send($view, $data, $callback);
		}
	}
}
