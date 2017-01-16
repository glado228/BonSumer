<?php

return [

	"required" => ":attribute kann nicht leer sein.",

	"is_correct_password" => "Das Passwort ist nicht richtig",

	"accepted"             => ":attribute muss akzeptiert werden.",
	"active_url"           => ":attribute ist keine gültige URL.",
	"after"                => ":attribute kann nicht vor :date liegen.",
	"alpha"                => ":attribute darf nur aus Buchstaben bestehen.",
	"alpha_dash"           => ":attribute darf nur auf Buchstaben, Zahlen und Strichen bestehen.",
	"alpha_num"            => "Das :attribute darf nur aus Buchstaben und Zahlen bestehen.",
	"array"                => ":attribute muss ein Feld sein.",
	"before"               => "The :attribute must be a date before :date.",
	"between"              => [
		"numeric" => ":attribute muss zwischen :min und :max liegen.",
		"file"    => ":attribute muss zwischen :min und :max kilobytes liegen.",
		"string"  => ":attribute muss zwischen :min und :max Buchstaben haben",
		"array"   => ":attribute muss zwischen :min und :max Elemente haben.",
	],
	"boolean"              => ":attribute muss richtig oder falsch sein.",
	"confirmed"            => ":attribute-Bestätigung stimmt nicht überein.",
	"date"                 => ":attribute ist kein gültiges Datum.",
	"date_format"          => ":attribute stimmt nicht mit dem Format :format überein.",
	"different"            => ":attribute und :other müssen unterschiedlich sein.",
	"digits"               => ":attribute muss aus :digits Zahlen bestehen.",
	"digits_between"       => ":attribute muss zwischen :min und :max Zahlen liegen.",
	"email"                => ":attribute muss eine gültige Email-Adresse sein.",
	"filled"               => ":attribute ist ein Pflichtfeld.",
	"exists"               => ":attribute ist ungültig.",
	"image"                => ":attribute muss ein Bild sein.",
	"in"                   => ":attribute ist ungültig.",
	"integer"              => ":attribute darf nur aus ganzen Zahlen bestehen.",
	"ip"                   => "The :attribute muss eine gültige IP-Adresse sein.",
	"max"                  => [
		"numeric" => ":attribute darf nicht größer sein als :max.",
		"file"    => ":attribute darf nicht größer sein als :max kilobytes.",
		"string"  => ":attribute darf nicht länger sein als :max Zeichen.",
		"array"   => ":attribute darf nicht länger sein als :max Elemente.",
	],
	"mimes"                => ":attribute muss vom Typ :values sein.",
	"min"                  => [
		"numeric" => ":attribute muss mindestens :min sein.",
		"file"    => ":attribute muss mindestens :min kilobytes sein.",
		"string"  => ":attribute muss mindestens :min Zeichen lang sein.",
		"array"   => ":attribute muss mindestens :min Elemente haben.",
	],
	"not_in"               => "Auswahl: :attribute ist ungültig.",
	"numeric"              => ":attribute muss eine Zahl sein.",
	"regex"                => ":attribute Format ist ungültig.",
	"required"             => ":attribute ist erforderlich.",
	"required_if"          => ":attribute -Feld ist erforderlich, wenn :other gleich :value ist.",
	"required_with"        => ":attribute -Feld ist erforderlich, wenn :values vorhanden ist.",
	"required_with_all"    => ":attribute -Feld ist erforderlich, wenn :values vorhanden ist.",
	"required_without"     => ":attribute -Feld ist erforderlich, wenn :values nicht vorhanden ist.",
	"required_without_all" => ":attribute -Feld ist erforderlich, wenn keine :values vorhanden ist.",
	"same"                 => ":attribute und :other müssen übereinstimmen.",
	"size"                 => [
		"numeric" => ":attribute muss folgende Größe haben :size.",
		"file"    => ":attribute muss :size kilobytes haben.",
		"string"  => ":attribute muss :size Zeichen haben.",
		"array"   => ":attribute muss :size Elemente beinhalten.",
	],
	"unique"               => ":attribute ist bereits vergeben.",
	"url"                  => ":attribute -Format ist ungültig.",
	"timezone"             => ":attribute muss eine gültige Zeitzone sein.",

	/*

		Custom validation rules
	 */
	"noscripttags" => "Script tags sind nicht erlaubt",

	/*

|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [
		'current_password' => 'Altes Passwort',
		'new_password' => 'Neues Passwort',
		'password' => 'Passwort',
		'email' =>'E-Mail',
		'terms_and_conditions' => 'AGB',
		'message' => 'Nachricht'
	],

	'donation_sizes' => "Die Spende kann nur aus positiven ganzen Zahlen bestehen."

];
