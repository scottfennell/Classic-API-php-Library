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

//*********************
// Get a list of all apps accessible by the user
//*********************
$apps = $tv->getApps();


//*********************
// Get app data for a specific app_id
//*********************
$app = $tv->getApp(22087);


//*********************
// Get data for a table
//*********************
$table = $tv->getTable(62995);


//*********************
// Get data for a view
//*********************
$view = $tv->getView(289592);


//**************
// Get a record
//**************
$record = $tv->getRecord(77113576);


//******************
// Add a new record
//******************
$tableId = 62994;
$record = array(
    'Times Visited' => 1,
    'City'          => 'Denver',
    'State'         => 'CO',
    'Menu Link'     => 'http://www.google.com',
    'Restaurant'    => 'Fake Restaurant 1',
    'Address'       => '1555 Blake Street',
    'Hours'         => '11am - 11pm',
    'Phone Number'  => '720.524.4345',
    'Cuisine'       => 'Burgers',
    'Notes'         => 'Order by phone. No delivery.',
    'Zip'           => '80202'
);
$tv->addRecord($tableId, $record);


//**********************
// Add multiple records 
// (batch inserts)
//**********************
$tableId = 62994;
$records = array();
$records[] = array(
    'table_id' => $tableId, 
    'data' => array(
        'Times Visited' => 1,
        'City'          => 'Denver',
        'State'         => 'CO',
        'Restaurant'    => 'Fake Restaurant 2'
        'Address'       => '1555 Blake Street',
        'Phone Number'  => '720.524.4345',
        'Cuisine'       => 'Burgers',
        'Notes'         => 'Order by phone. No delivery.',
        'Zip'           => '80202'
    )
);
$records[] = array(
    'table_id' => $tableId, 
    'data' => array(
        'Times Visited' => 3,
        'City'          => 'Denver',
        'State'         => 'CO',
        'Restaurant'    => 'Fake Restaurant 3'
        'Address'       => '1555 Blake Street',
        'Phone Number'  => '720.524.4345',
        'Cuisine'       => 'Burgers',
        'Notes'         => 'Order by phone. No delivery.',
        'Zip'           => '80202'
    )
);
$tv->addRecords($records);


//************************
// Update a single record
//************************
$recordId = 105973377;
$record = array(
    'Times Visited' => 5,
    'Restaurant'    => 'Fake Restaurant Updated!!'
);
$tv->updateRecord($recordId, $record);


//************************
// Update multiple records
// (batch updates)
//************************
$records = array();
// 1st record
$records[] = array(
    'id'   => 105973376, // record id
    'data' => array(
        'Times Visited' => 12,
        'Restaurant'    => 'Fake Restaurant Updated2!!'
    )
);
// 2nd record
$records[] = array(
    'id'   => 105973377, // record id
    'data' => array(
        'Times Visited' => 13,
        'Restaurant'    => 'Fake Restaurant Updated3!!'
    )
);
$tv->updateRecords($records);


//************************
// Delete a single record
//************************
$tv->deleteRecord(105973364);


//************************
// Delete a multiple records
// (batch delete)
//************************
$tv->deleteRecords(array(105973376, 105973377));
*/