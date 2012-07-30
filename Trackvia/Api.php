<?php
namespace Trackvia;

require 'Request.php';
require 'Authentication.php';

use Trackvia\Request;
use Trackvia\Authentication;

class Api
{
    const BASE_URL = 'https://secure.trackviadev.com/';

    // URLs for API data endpoints
    const APPS_URL = 'apps/';
    const TABLES_URL = 'tables/';
    const VIEWS_URL = 'views/';
    const RECORDS_URL = 'records/';

    /**
     * Error message received back from api endpoint if access token is expired
     */
    const EXPIRED_ACCESS_TOKEN = 'The access token provided has expired.';

    /**
     * Error message received back from api endpoint if refresh token is expired
     */
    const EXPIRED_REFRESH_TOKEN = 'The refresh token provided has expired.';

    /**
     * Object to handle http requests
     * @var TrackviaRequest
     */
    private $request;

    /**
     * Object to handle API authentication
     * @var TrackviaAuthentication
     */
    private $auth;

    private $lastRequest;

    /**
     * Array of events to assign callbacks to.
     * @var array
     */
    private $events = array();

    public function __construct($params)
    {
        $this->request = new Request();
        $this->auth    = new Authentication($this->request, $params);
    }

    /**
     * Authenticate the user with OAuth2
     * @return array Access token data
     */
    public function authenticate()
    {
        return $this->auth->authenticate();
    }

    /**
     * Set the tokend ata for authentication
     * @param [type] $tokenData [description]
     */
    public function setTokenData($tokenData)
    {
        $this->auth->setTokenData($tokenData);
    }

    /**
     * Attach an event listener.
     * @param  string $event
     * @param  string|array $callback A callback function name that can be passed into call_user_func()
     */
    public function on($event, $callback)
    {
        // if (!array_key_exists($event, $this->events)) {
        //     throw new Exception("Cannot bind to event \"$event\". This event is not supported.");
        // }
        // 
        if (!isset($this->events[$event])) {
            // initialize an array for this event name
            $this->events[$event] = array();
        }

        if (!is_callable($callback)) {
            throw new Exception('Callback cannot is not callable. Check you have the right function name.');
        }

        $this->events[$event][] = $callback;
    }

    /**
     * Trigger a binded event.
     * This will call all binded callback functions for a given event.
     * @param string $event The event name
     */
    public function trigger($event)
    {
        if (!array_key_exists($event, $this->events)) {
            throw new Exception("Cannot trigger event \"$event\". This event is not supported.");
        }

        // set different args based on event name
        switch ($event) {
            case 'new_token':
                $data = array(
                    'access_token'  => $this->accessToken,
                    'refresh_token' => $this->refresh,
                    'expires_at'    => $this->expiresAt
                );
                break;
        }
        // loop through each callback for this event
        foreach ($this->events[$event] as $callback) {
            call_user_func($callback, $data);
        }
    }

    /**
     * Check if the response failed and if the token is expired.
     * If it is expired, use the refresh token to get a new one
     * @param  array $response 
     * @return boolean
     */
    private function checkResponse($response)
    {
        if (is_array($response) && isset($response['error_description'])) {
            if ($response['error_description'] == self::EXPIRED_ACCESS_TOKEN) {

            }
        }
    }

    private function api($url, $httpMethod = 'GET')
    {
        // check for access token
        // if (!$this->auth->hasAccessToken() || $this->auth->isAccessTokenExpired()) {
            
        // }
        $this->authenticate();

        $accessToken = $this->auth->getAccessToken();

        if (!$accessToken) {
            // should have a token at this point
            // if not, something went wrong
            throw new Exception('Cannot make an api request without an access token');
        }

        // save this request in case we need to use the refresh token
        $this->lastRequest = array(
            'url'  => $url
            // 'data' => $data
        );

        // make the request with current access token
        $response = $this->request->request($url, $httpMethod, array(
            'access_token' => $accessToken
        ));

        $this->trigger('api_request', array('url' => $url));

        $this->checkResponse($response);

        if (!$response && $this->request->isTokenExpired()) {
            //use refresh token to request new access token
            if ($this->authenticate()) {
                // redo initial api request
            }
        }
    }

    /**
     * Get a list of all apps, or a single app.
     * 
     * Optional app_id parameter lets you specify one app if you want.
     * Leave is empty to get all apps back.
     * 
     * @param  integer $appId
     * @return array   Array of app data returned from the api
     */
    public function getApps($appId = null)
    {
        if ($appId != null && !is_int($appId)) {
            throw new Exception('App ID must be an integer');
        }

        // build the url
        $url = self::BASE_URL . self::APPS_URL . $appId;
            
        return $this->api($url, 'GET');
    }

    /**
     * Get data for a single app by app_id.
     * This will provide you with all the tables available for this app.
     * 
     * @param  integer $appId
     * @return array   Array of app data returned fromt the api
     */
    public function getApp($appId)
    {
        $this->getApps($appId);
    }

    /**
     * Get table data back for a table_id.
     * This will provide you all the views available for this table.
     * 
     * @param  integer $tableId
     * @return array   Array of table data returned from the api
     */
    public function getTable($tableId)
    {
        if (!is_int($tableId)) {
            throw new Exception('Table ID must be an integer');
        }

        // build the url
        $url = self::BASE_URL . self::TABLES_URL . $tableId;
            
        return $this->api($url, 'GET');
    }

    /**
     * Get view data back for a view_id.
     * This will provide you with all the records under this view.
     * 
     * @param  integer $viewId
     * @return array   Array of view data returned from the api
     */
    public function getView($viewId)
    {
        if (!is_int($viewId)) {
            throw new Exception('View ID must be an integer');
        }

        // build the url
        $url = self::BASE_URL . self::VIEWS_URL . $viewId;
            
        return $this->api($url);
    }

    /**
     * Get Record data back for a record_id.
     * This will provide you with all the column data for a record.
     * 
     * @param  integer $recordId
     * @return array   Array of Record data returned from the api
     */
    public function getRecord($recordId)
    {
        if (!is_int($recordId)) {
            throw new Exception('Record ID must be an integer');
        }

        // build the url
        $url = self::BASE_URL . self::RECORDS_URL . $recordId;
            
        return $this->api($url);
    }

    public function addRecord($data)
    {
        # code...
    }

    public function addRecords($data)
    {
        # code...
    }

    public function updateRecord($id)
    {
        # code...
    }

    public function updateRecords($data)
    {
        # code...
    }

    public function deleteRecord($id)
    {
        # code...
    }

    public function deleteRecords($data)
    {
        # code...
    }

    public function search($tableId, $term)
    {
        # code...
    }

}