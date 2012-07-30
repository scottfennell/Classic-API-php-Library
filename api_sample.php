<?php
require 'Trackvia/Api.php';
require 'Trackvia/Log.php';

use Trackvia\Api;
use Trackvia\Log;

$clientId = '13_2s6wg16cwtk48kcgggo8kcgow44w0k8k4800ssw4oss0coc0g8';
$clientSecret = '4htbckzh1qm8wo4s88gw44g8gs80g00so0sg0kw8kkoccco8gg';

$username = 'api.tester';
$password = 'co3823se';

function save_token_data($tokenData, $extraParams)
{
	$accessToken = $tokenData['access_token'];
	$refreshToken = $tokenData['refresh_token'];
	
	// timestamp when the access token expires
	$expiresAt = $tokenData['expires_at'];

	// code to save token data to your database
}

// load the saved token for this user
function load_saved_token_data()
{
	// code to load the token from your database
	
	return array(
		'access_token'  => 'ZDA4ZTAzZWNhODI1YzViNGU0YjUwOWU2ZWUxMTljYzUxYzc0YTExMjViNTYyYWYwMDcxYTliZGM1ZTBiMjJhMANULL',
		'refresh_token' => '',
		'expires_at'    => ''
	);
}

$tokenData = load_saved_token_data();
$extraParams = array('this can be whatever you want');

// Create a TrackviaApi object with your clientId and secret.
// The client_id and secret are only user when you need to request a new access token.
$tv = new Api(array(
	'client_id'      => $clientId,
	'client_secret'  => $clientSecret,
	'username'       => $username,
	'password' 		 => $password
));

// setup the logger for debugging
$log = new Log($tv);

// attach a listener function for when a new token is generated so you can save it to a database
$tv->on('new_token', 'save_token_data', $extraParams);

// If there is saved token data to use, set it now
if (!empty($savedToken) && isset($savedToken['access_token'])) {
	$tv->setTokenData(array(
		'access_token'  => $savedToken['access_token'],
		'refresh_token' => $savedToken['refresh_token'],
		'expires_at'    => $savedToken['expires_at']
	));
}

if (!$tokenData) {
	// request a new access token with user credentials you already passed in
	// this will trigger the "new_token" listener you created above so you can save the token data
	$tv->authenticate();

} else {
	// set the current token data you have
	// the library will auto check the expiresAt so we don't waste a request
	// If it is expired, a new access token 
	$tv->setTokenData(array(
		'access_token'  => $tokenData['access_token'],
		'refresh_token' => $tokenData['refresh_token'],
		'expires_at'    => $tokenData['expires_at']
	));
}


$appId = 1;

if ($apps = $tv->getApps()) {
	// do something with apps list
	// var_dump($apps); exit;
} else {
	// request failed
	// did access token expire?
	
}


var_dump($apps); exit;