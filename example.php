<?php
require 'Trackvia/Api.php';
require 'Trackvia/Log.php';

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '');
define('CLIENT_SECRET', '');
define('USERNAME', '');
define('PASSWORD', '');

// Create a TrackviaApi object with your clientId and secret.
// The client_id and secret are only used when you need to request a new access token.
$tv = new Api(array(
    'client_id'     => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'username'      => USERNAME,
    'password'      => PASSWORD    
));

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Trackvia API v2 Example</title>
    </head>    
    <body>
        
        <h1>Trackvia API v2 Example</h1>
        <?php 
            if(CLIENT_ID == '' || CLIENT_SECRET == '' || USERNAME == '' || PASSWORD == ''){
                ?>
                <h2>Quick Start</h2>
                <p>
                    Your example code requires your client_id, client_secret, username and password. You can get these
                    at the <a href="https://secure.trackvia.com/accountsettings/">Trackvia account settings page</a>. 
                    Your username and password are the username and password you use to login to TrackVia.                    
                </p>
                <p>
                    Once you have your credentials, update them in the top of the example.php file.
                </p>
                <?php
            } else {
        ?>
            <p>            
                Below is a simple example to show you how to get a list of all your TrackVia apps.
            </p>
            <?php 
                /*
                 * Once you have created an api object, you can then use it to quary data from the api. $tv->getApps() will
                 * get a list of all the apps that you have in your account along with the app id. 
                 */
                $apps = $tv->getApps();            
            ?>
            <h2>Your Trackvia Apps</h2>
            <ul>
            <?php
                /**
                 * The results are an array of objects similar to this
                 * array(
                 *     array(
                 *          id => 432
                 *          name => "Some App"
                 *     ),
                 *     array(
                 *          id => 3543
                 *          name => "Some other app"
                 *     )
                 * )
                 */
                foreach ($apps as $app) {
                    echo "<li>".$app['name']."</li>";
                }
            ?>
            </ul>
        <?php } ?>
    </body>
</html>

