<?php
namespace Trackvia;

require_once 'EventDispatcher.php';
require_once 'Request.php';
require_once 'Authentication.php';

class Api extends EventDispatcher
{
    const BASE_URL = 'https://api.trackvia.com/';

    // URLs for API endpoints
    const DASHBOARDS_URL = 'dashboards';
    const FORMS_URL      = 'forms';
    const APPS_URL       = 'apps';
    const TABLES_URL     = 'tables';
    const VIEWS_URL      = 'views';
    const RECORDS_URL    = 'records';
    const SEARCH_URL     = 'search';

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

    /**
     * Whether or not the current access token is expired.
     * This gets flagged true when an api request is made and 
     * the response comes back as an error indicating expired token.
     * 
     * @var boolean
     */
    private $isTokenExpired = false;

    public function __construct($params)
    {
        $this->request = new Request();
        $this->auth    = new Authentication($this->request, $params);

        // add an event listener for a new token on the authentication object
        $this->auth->on('new_access_token', array($this, 'onNewAccessToken'));
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
     * Method to handle the new_token even trigger by the authentication class
     * Bubble up the event with token data for the client.
     */
    public function onNewAccessToken($data)
    {
        $this->trigger('new_token', $data);
    }

    /**
     * Set the token data for authentication
     * @param array $tokenData
     */
    public function setTokenData($tokenData)
    {
        $this->auth->setTokenData($tokenData);
    }

    public function setUserCredentials($username, $password)
    {
        $this->auth->setUserCreds($username, $password);
    }

    public function getAuthentication()
    {
        return $this->auth;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Check if the response failed and if the token is expired.
     * 
     * Any errors returned from the API server will be thrown as an Exception.
     * 
     * @param  array $response 
     * @return boolean
     */
    private function checkResponse()
    {
        $response = $this->request->getResponse();
        if (is_array($response) && isset($response['error_description'])) {
            switch ($response['error_description']) {
                case self::EXPIRED_ACCESS_TOKEN:
                    $this->isTokenExpired = true;
                    // return here so we don't throw this error
                    // so we can use the refresh token
                    return false;
            }

            // throw an \Exception with the returned error message
            throw new \Exception('API Error :: ' . $response['error_description']);
        }

        return true;
    }

    /**
     * Make an api request.
     * 
     * @param  string $url
     * @param  string $httpMethod The http method to use with this request
     * @param  string $data Optional data to send with request
     * @param  string $contentType
     * @return array The json parsed response from the server
     */
    private function api($url, $httpMethod = 'GET', $data = null, $contentType = null)
    {
        // trigger an event
        $this->trigger('api_request_init', array('url' => $url));

        $this->authenticate();

        $accessToken = $this->auth->getAccessToken();
        if (!$accessToken) {
            // should have a token at this point
            // if not, something went wrong
            throw new \Exception('Cannot make an api request without an access token');
        }

        // save this request in case we need to use the refresh token
        $lastRequest = array(
            'url'    => $url,
            'method' => $httpMethod,
            'data'   => $data
        );

        // add the access token onto the url
        $url = $url . '?access_token='.$accessToken;

        $this->trigger('api_request_send', array('url' => $url, 'http_method' => $httpMethod, 'data' => $data));
        
        $this->request
            ->setMethod($httpMethod)
            ->setData($data);

        if ($contentType) {
            $this->request->setContentType("application/$contentType");
        }
        // now send the request
        $this->request->send($url);

        $this->trigger('api_request_complete', array('url' => $url, 'response' => $this->request->getResponse()));

        // check the response for any errors
        $vaild = $this->checkResponse();

        if (!$vaild && $this->isTokenExpired) {
            // blow out the current token so a new one gets requested
            $this->auth->clearAccessToken();

            // redo the last api request
            $this->api(
                $lastRequest['url'],
                $lastRequest['method'],
                $lastRequest['data']
            );
        }

        return $this->request->getResponse();
    }

    /**
     * Get a list of all dashboards, or a single dashboard.
     * 
     * Optional id parameter lets you specify one dashboard if you want.
     * Leave is empty to get all dashboards back.
     * 
     * @param  int $id
     * @return array Array of dashboard data returned from the api
     */
    public function getDashboards($id = null)
    {
        // build the url
        $url = self::BASE_URL . self::DASHBOARDS_URL . ($id ? '/'.$id : '');
            
        return $this->api($url, 'GET');
    }

    /**
     * Get data for a single dashboard.
     * 
     * @param  int $id
     * @return array
     */
    public function getDashboard($id)
    {
        return $this->getDashboards($id);
    }

    /**
     * Get a list of all apps, or a single app.
     * 
     * Optional app_id parameter lets you specify one app if you want.
     * Leave is empty to get all apps back.
     * 
     * @param  int $appId
     * @return array   Array of app data returned from the api
     */
    public function getApps($appId = null)
    {
        // build the url
        $url = self::BASE_URL . self::APPS_URL . ($appId ? '/'.$appId : '');
            
        return $this->api($url, 'GET');
    }

    /**
     * Get data for a single app by app_id.
     * This will provide you with all the tables available for this app.
     * 
     * @param  int $appId
     * @return array Array of app data returned fromt the api
     */
    public function getApp($appId)
    {
        return $this->getApps($appId);
    }

    /**
     * Get table data back for a table_id.
     * This will provide you all the views available for this table.
     * 
     * @param  int $tableId
     * @return array Array of table data returned from the api
     */
    public function getTable($tableId)
    {
        // build the url
        $url = self::BASE_URL . self::TABLES_URL .'/'. $tableId;
            
        return $this->api($url, 'GET');
    }

    public function getTableForeignKeyValues($tableId, $fkId)
    {
        $url = self::BASE_URL . self::TABLES_URL .'/'. $tableId . '/foreign_keys/' . $fkId;
        return $this->api($url, 'GET');
    }

    /**
     * Get view data back for a view_id.
     * This will provide you with all the records under this view.
     * 
     * @param  int $viewId
     * @return array Array of view data returned from the api
     */
    public function getView($viewId)
    {
        // build the url
        $url = self::BASE_URL . self::VIEWS_URL .'/'. $viewId;
        
        return $this->api($url, 'GET');
    }

    /**
     * Get Record data back for a record_id.
     * This will provide you with all the column data for a record.
     * 
     * @param  int $id
     * @return array Array of Record data returned from the api
     */
    public function getRecord($id)
    {
        // build the url
        $url = self::BASE_URL . self::RECORDS_URL .'/'. $id;
            
        return $this->api($url, 'GET');
    }

    /**
     * Add a new record to a table
     * @param int $id
     * @param array $record
     * @return array
     */
    public function addRecord($id, $record)
    {
        $url = self::BASE_URL . self::RECORDS_URL;
        $data = array(
            'table_id' => $id,
            'records'  => array($record)
        );
        return $this->api($url, 'POST', json_encode($data), 'json');
    }

    /**
     * Add more than one record at once to a table. Batch inserts.
     * 
     * @param  int   $tableId
     * @param  array $records
     * @return array
     */
    public function addRecords($tableId, $records)
    {
        $url = self::BASE_URL . self::RECORDS_URL;
        $data = array(
            'table_id' => $tableId,
            'records'  => $records
        );
        return $this->api($url, 'POST', json_encode($data), 'json');
    }

    /**
     * Update a single record.
     * 
     * @param  int $id
     * @param  array $data
     * @return array
     */
    public function updateRecord($id, $data)
    {
        $url = self::BASE_URL . self::RECORDS_URL .'/'. $id;
        return $this->api($url, 'PUT', json_encode($data), 'json');
    }

    /**
     * Update multiple records.
     * 
     * @param  int $tableId
     * @param  array $records
     * @return array
     */
    public function updateRecords($tableId, $records)
    {
        $url = self::BASE_URL . self::RECORDS_URL;
        $data = array(
            'table_id' => $tableId,
            'records'  => $records
        );
        return $this->api($url, 'PUT', json_encode($data), 'json');
    }

    /**
     * Delete a record by id.
     * 
     * @param  int $id
     */
    public function deleteRecord($id)
    {
        $url = self::BASE_URL . self::RECORDS_URL .'/'. $id;
        return $this->api($url, 'DELETE');
    }

    /**
     * Delete multiple records at once. Batch delete.
     * 
     * @param  int $tableId
     * @param  array $records
     */
    public function deleteRecords($tableId, $records)
    {
        $url = self::BASE_URL . self::RECORDS_URL;
        $data = array(
            'table_id' => $tableId,
            'records'  => $records
        );
        return $this->api($url, 'DELETE', json_encode($data), 'json');
    }

    /**
     * Get the forms for a table.
     * @param  int $tableId  The id of the table
     * @return array
     */
    public function getForms($tableId)
    {
        $url = self::BASE_URL . self::TABLES_URL .'/'. $tableId .'/'. self::FORMS_URL;
            
        return $this->api($url, 'GET');
    }

    /**
     * Get data for a specific form
     * @param  int $formId  The id of the form
     * @return array
     */
    public function getForm($formId)
    {
        $url = self::BASE_URL . self::FORMS_URL .'/'. $formId;
            
        return $this->api($url, 'GET');
    }

    /**
     * Search for records on a table.
     * 
     * @param  int $tableId
     * @param  string $term
     * @return array
     */
    public function search($tableId, $term)
    {
        $url = self::BASE_URL . self::SEARCH_URL .'/'. $tableId .'/'. urlencode($term);
        return $this->api($url, 'GET');
    }

}