# Trackvia API
## PHP Library

This library requires that you are at least using PHP 5.3

**Code examples are available in example.php**

The Trackvia API uses OAuth2 for authentication. This PHP library minimizes the work you need to do for OAuth2. 
Just provide the proper credentials and token data when needed and the library will handle the authentication for you.

### Authentication flow (if you're interested):
* Request an access token on behalf of the user. Access tokens are unique per user. You will need the user's credentials to request the first access token.
* You will get back an 'access\_token', 'expires\_at' timestamp, and a 'refresh\_token'. Save this information.
* Now you can make an API request using the access token.
* When the access token expires (in 24 hours), you can request a new one using the refresh token.
* Once the refresh token expires (in 7 days), you go back to the first step of using user credentials to get an access token.


# The code
### First, create a new Api object using your client credentials.

    $tv = new Api(array(
        'client_id'     => 'your_client_id',
        'client_secret' => 'your_client_secret'
        
        // You can optionally pass in the user credentials here too
        // 'username'      => 'sample_username',
        // 'password'      => 'sample_password'
    ));

Your client ID and secret are specific to your account and are accessible from within your Trackvia account settings.
You must always set the client_id and secret so that the library can automatically request a new token for you.


### Add a listener function
It is up to you to save the access token when it is obtained automatically.
The Api class will trigger an event when a new Access Token has been acquired.
You can attach a closure function to this event that handles saving the token data to your database.
    
    $tv->on('new_token', function ($tokenData, $extraParams) {
        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'];
        
        // timestamp when the access token expires
        $expiresAt = $tokenData['expires_at'];

        //============
        // code to save token data to your database
        //============

    }, $extraParams);

The important pieces of data to save are 'access\_token', 'refresh\_token', and 'expires\_at'.

`$extraParams` is an optional array of values that can be attached with the listener function.
This array will then be passed into the function when it is called.


### No access token yet?
If you don't have an access token yet, then you need to set the user's credentials (username, password) before making API requests.

    $tv->setUserCreds('sample_username', 'sample_password');


### Already have an access token?
If you are loading a saved token, set it before making any requests
    
    $tv->setTokenData(array(
        'access_token'  => $savedToken['access_token'],
        'refresh_token' => $savedToken['refresh_token'],
        'expires_at'    => $savedToken['expires_at']
    ));


### Now make an API request
When a request is successful (200 OK), data will be returned in array format.

Any errors returned from the API server will be thrown as PHP exceptions.


    //*********************
    // Get a list of dashboards
    //*********************
    $dashboards = $tv->getDashboards();

    //*********************
    // Get a single dashboard by dashboard_id
    //*********************
    $dashboards = $tv->getDashboard(2);


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
    // Get a list of forms for a table
    //*********************
    $forms = $tv->getForms(62995);

    //*********************
    // Get data for a specific form
    //*********************
    $form = $tv->getForm(3);


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
    // 1st record
    $records[] = array(
        'Times Visited' => 1,
        'City'          => 'Denver',
        'State'         => 'CO',
        'Restaurant'    => 'Fake Restaurant 2',
        'Address'       => '1555 Blake Street',
        'Phone Number'  => '720.524.4345',
        'Cuisine'       => 'Burgers',
        'Notes'         => 'Order by phone. No delivery.',
        'Zip'           => '80202'
    );
    // 2nd record
    $records[] = array( 
        'Times Visited' => 3,
        'City'          => 'Denver',
        'State'         => 'CO',
        'Restaurant'    => 'Fake Restaurant 3',
        'Address'       => '1555 Blake Street',
        'Phone Number'  => '720.524.4345',
        'Cuisine'       => 'Burgers',
        'Notes'         => 'Order by phone. No delivery.',
        'Zip'           => '80202'
    );
    $tv->addRecords($tableId, $records);


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
    $tableId = 62994;
    $records = array();
    // 1st record
    $records[] = array(
        'id'   => 105976918, // record id
        'fields' => array(
            'Times Visited' => 12,
            'Restaurant'    => 'Fake Restaurant Updated2!!'
        )
    );
    // 2nd record
    $records[] = array(
        'id'   => 105976919, // record id
        'fields' => array(
            'Times Visited' => 13,
            'Restaurant'    => 'Fake Restaurant Updated3!!'
        )
    );
    $tv->updateRecords($tableId, $records);


    //************************
    // Delete a single record
    //************************
    $tv->deleteRecord(105973364);


    //************************
    // Delete a multiple records
    // (batch delete)
    //************************
    $tableId = 62994;
    $tv->deleteRecords($tableId, array(105976918, 105976919));