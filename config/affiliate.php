<?php


return [

	'zanox' => [

		'connect_id' => env('ZANOX_CONNECT_ID'),
		'secret_key' => env('ZANOX_SECRET_KEY')
	],

	'affilinet_1' => [

		'username' => env('AFFILINET_USERNAME_1'),
		'password' => env('AFFILINET_PASSWORD_1')
	],

	'affilinet_2' => [

		'username' => env('AFFILINET_USERNAME_2'),
		'password' => env('AFFILINET_PASSWORD_2')
	],

	'belboon' => [

		'login' => env('BELBOON_USERNAME'),
		'password' => env('BELBOON_PASSWORD')
	],

	'adcell' => [

		'username' => env('ADCELL_USERNAME'),
		'password' => env('ADCELL_PASSWORD')
	],

	'webgains' => [

		'login' => env('WEBGAINS_USERNAME'),
		'password' => env('WEBGAINS_PASSWORD'),
		'campaignId' => '152419'
	],

	'tradedoubler' => [

		'key' => env('TRADEDOUBLER_KEY'),
		'pagename' => env('TRADEDOUBLER_PAGENAME')
	],

	'cj' => [

		'pid' => env('CJ_PID'),
		'devkey' => env('CJ_DEVKEY')
	]
];
