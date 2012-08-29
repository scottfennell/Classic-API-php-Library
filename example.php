<?php
require 'Trackvia/Api.php';
require 'Trackvia/Log.php';

use Trackvia\Api;
use Trackvia\Log;

// Create a TrackviaApi object with your clientId and secret.
// The client_id and secret are only used when you need to request a new access token.
$tv = new Api(array(
    'client_id'     => 'your_client_id',
    'client_secret' => 'your_client_secret'
    
    // You can optionally pass in the user credentials here too
    // 'username'      => 'sample_username',
    // 'password'      => 'sample_password'
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
    $tv->setUserCreds('sample_username', 'sample_password');
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