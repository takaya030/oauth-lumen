# OAuth wrapper for Lumen 6

oauth-lumen is a simple lumenl 6 service provider (wrapper) for [Lusitanian/PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib) 
which provides oAuth support in PHP 7.2+ and is very easy to integrate with any project which requires an oAuth client.

Was first developed by [Artdarek](https://github.com/artdarek/oauth-4-laravel) for Laravel 4 and I ported it to Lumen 6.

---

- [Supported services](#supported-services)
- [Installation](#installation)
- [Registering the Package](#registering-the-package)
- [Configuration](#configuration)
- [Usage](#usage)
- [Basic usage](#basic-usage)
- [More usage examples](#more-usage-examples)

## Supported services

The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below.

Included service implementations:

- OAuth1
    - 500px
    - BitBucket
    - Etsy
    - FitBit
    - Flickr
    - QuickBooks
    - Scoop.it!
    - Tumblr
    - Twitter
    - Xing
    - Yahoo
- OAuth2
    - Amazon
    - BitLy
    - Bitrix24
    - Box
    - Buffer
    - Dailymotion
    - Delicious
    - Deezer
    - DeviantArt
    - Dropbox
    - Eve Online
    - Facebook
    - Foursquare
    - GitHub
    - Google
    - Harvest
    - Heroku
    - Hubic
    - Instagram
    - Jawbone UP
    - LinkedIn
    - Mailchimp
    - Microsoft
    - Mondo
    - Nest
    - Netatmo
    - Parrot Flower Power
    - PayPal
    - Pinterest
    - Pocket
    - Reddit
    - RunKeeper
    - Salesforce
    - SoundCloud
    - Spotify
    - Strava
    - Ustream
    - Vimeo
    - Vkontakte
    - Yahoo
    - Yammer
- more to come!

## Installation

Add oauth-lumen to your composer.json file:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/takaya030/oauth-lumen"
    }
],
"require": {
  "takaya030/oauth-lumen": "dev-master"
}
```

Use composer to install this package.

```
$ composer update
```

### Registering the Package

Create oauth service provider ```app/Providers/OAuthServiveProvider.php```

```php
<?php

namespace App\Providers;

/**
 * @author     Dariusz Prz?da <artdarek@gmail.com>
 * @copyright  Copyright (c) 2013
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use Illuminate\Support\ServiceProvider;
use Takaya030\OAuth\OAuth;

class OAuthServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'oauth'
        $this->app->singleton('oauth', function ($app) {
            // create oAuth instance
            $oauth = new OAuth();

            // return oAuth instance
            return $oauth;
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
```

Register the service provider in ```bootstrap/app.php```:

```php
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\OAuthServiceProvider::class);
```

## Configuration

Create configuration file manually in config directory ``config/oauth-lumen.php`` and put there code from below.

```php
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
```

Load the configure in ```bootstrap/app.php```

```php
/*
|--------------------------------------------------------------------------
| Load Custom Config Files
|--------------------------------------------------------------------------
*/

$app->configure('oauth-lumen');
```

### Credentials

Add your credentials to ``config/oauth-lumen.php`` (depending on which option of configuration you choose)


The `Storage` attribute is optional and defaults to `Session`. 
Other [options](https://github.com/Lusitanian/PHPoAuthLib/tree/master/src/OAuth/Common/Storage).

## Usage

### Basic usage

Just follow the steps below and you will be able to get a [service class object](https://github.com/Lusitanian/PHPoAuthLib/tree/master/src/OAuth/OAuth2/Service) with this one rule:

```php
$service = app('oauth')->consumer('Google');
```

Optionally, add a second parameter with the URL which the service needs to redirect to, otherwise it will redirect to the current URL.

```php
$service = app('oauth')->consumer('Google', 'http://url.to.redirect.to');
```

## Usage examples

###Google

Configuration:
Add your Google credentials to ``config/oauth-lumen.php``

```php
'Google' => [
    'client_id'     => 'Your Google client ID',
    'client_secret' => 'Your Google Client Secret',
    'scope'         => ['userinfo_email', 'userinfo_profile'],
],	
```

In your Controller use the following code:

```php

public function loginWithGoogle(Request $request)
{
	// get data from request
	$code = $request->get('code');
	
	// get google service
	$googleService = app('oauth')->consumer('Google');
	
	// check if code is valid
	
	// if code is provided get user data and sign in
	if ( ! is_null($code))
	{
		// This was a callback request from google, get the token
		$token = $googleService->requestAccessToken($code);
		
		// Send a request with it
		$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
		
		$message = 'Your unique Google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
		echo $message. "<br/>";
		
		//Var_dump
		//display whole array.
		dd($result);
	}
	// if not ask for permission first
	else
	{
		// get googleService authorization
		$url = $googleService->getAuthorizationUri();
		
		// return to google login url
		return redirect((string)$url);
	}
}
```

### More usage examples:

For examples go [here](https://github.com/Lusitanian/PHPoAuthLib/tree/master/examples)

