<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => '\\OAuth\\Common\\Storage\\Session',
	//'storage' => '\\Takaya030\\OAuth\\OAuthLumenSession',

	/**
	 * Consumers
	 */
	'consumers' => [

		'Google' => [
			'client_id'     => '',
			'client_secret' => '',
			'scope'         => [],
		],

	]

];
