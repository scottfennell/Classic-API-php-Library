<?php
/* advanced_example
 * This shows off a few extra features of the Trackvia API. In this example, we connect to trackvia and save the 
 * token to the database for future use.
 * 
 * @copyright Copyright (c) 2013, TrackVia Inc.
 */
require 'Trackvia/Api.php';
require 'Trackvia/Log.php';

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '');
define('CLIENT_SECRET', '');
define('USERNAME', '');
define('PASSWORD', '');
if(CLIENT_ID == '' || CLIENT_SECRET == '' || USERNAME == '' || PASSWORD == ''){ 
    die("Please setup your access credentials");    
}

// Create a TrackviaApi object with your clientId and secret.
// The client_id and secret are only used when you need to request a new access token.
$tv = new Api(array(
    'client_id'     => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,    
));

// load the saved token for this user
function load_saved_token_data()
{
    //===========
    // code to load the token from your database
    //===========
    
    // return array(
    //     'access_token'  => 'user_access_token',
    //     'refresh_token' => 'user_refresh_token',
    //     'expires_at'    => 'expires_timestamp'
    // );
}
$savedToken = load_saved_token_data();

// If there is saved token data to use, set it now
if (!empty($savedToken) && isset($savedToken['access_token'])) {

    $tv->setTokenData(array(
        'access_token'  => $savedToken['access_token'],
        'refresh_token' => $savedToken['refresh_token'],
        'expires_at'    => $savedToken['expires_at']
    ));

} else {
    // No token data.
    // So you need to get the user credentials for authentication.
    $tv->setUserCredentials(USERNAME, PASSWORD);
}

// extra parameters to pass in for the "new_token" event callback
$extraParams = array('extra_params' => 'this can be whatever you want');

// attach a listener function for when a new token is generated so you can save it to a database
$tv->on('new_token', function ($tokenData, $extraParams) {
    $accessToken = $tokenData['access_token'];
    $refreshToken = $tokenData['refresh_token'];
    
    // timestamp when the access token expires
    $expiresAt = $tokenData['expires_at'];

    //============
    // code to save token data to your database
    //============

}, $extraParams);

/*

//********************* 
// Setup the logger for debugging (optional)
//*********************
$authLog = new Log($tv->getAuthentication());
$log = new Log($tv);

*/

/**
 * Get a list of your apps
 */
$apps = $tv->getApps();       
var_dump($apps);