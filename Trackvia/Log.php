<?php
namespace Trackvia;

class Log 
{
    private $log;
    private $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;

        // pass in the events we want to listen to
        $this->initListeners(array(
            // Api class
            // 'authenticate',
            'new_access_token',
            'api_request_init',
            'api_request_send',
            'api_request_complete',

            //Authentication class
            'is_token_valid',
            'has_refresh_token',
            'request_token_with_user_creds',
            'request_token_with_refresh_token',
            'refresh_token_expired',
            'no_authentication_tokens',
            'authenticate_with_user_creds',
            'no_authentication'
        ));
    }

    private function logInfo($text)
    {
        echo "INFO :: " . get_class($this->subject) . " :: $text\n";
    }

    /**
     * Setup the event listeners so we can log certain events.
     * Events name are auto mapped to method name on_$eventname
     * @param  array $events
     */
    private function initListeners($events)
    {
        foreach ($events as $event) {
            $this->subject->on($event, array($this, 'on_' . $event));
        }
    }

// Api event listeners
    public function on_authenticate($data)
    {
        $this->logInfo("Authenticating with Trackvia server");
    }

    public function on_new_access_token($data)
    {
        $this->logInfo('New access token granted');
        print_r($data);
    }

    public function on_api_request_init($data)
    {
        $url = $data['url'];
        $this->logInfo("Init api request to url \"$url\"");
    }

    public function on_api_request_send($data)
    {
        $url = $data['url'];
        $method = $data['http_method'];
        $this->logInfo("Sending $method request to url \"$url\"");
        
        if (isset($data['data'])) {
            $this->logInfo("Sending request body - " . $data['data']);
        }
    }

    public function on_api_request_complete($data)
    {
        $url = $data['url'];
        $this->logInfo("Request Complete");
        echo "API Response Data:\n";
        print_r($data['response']);
    }


// Authentication event listeners
    public function on_is_token_valid($data)
    {
        $this->logInfo("Is current access token valid? " . ($data['is_valid'] ? 'Yes' : 'No') );
    }

    public function on_has_refresh_token($data)
    {
        $this->logInfo("Is there a refresh token? " . ($data['refresh_token'] ? 'Yes' : 'No') );
    }

    public function on_request_token_with_user_creds($data)
    {
        $username = $data['username'];
        $password = $data['password'];
        $this->logInfo("Requesting new access token with user creds - user: $username, pass: $password");
    }

    public function on_request_token_with_refresh_token($data)
    {
        $refresh_token = $data['refresh_token'];
        $this->logInfo("Requesting new access token with refresh token - token: $refresh_token");
    }

    public function on_refresh_token_expired()
    {
        $this->logInfo("Refresh token is expired");
    }

    public function on_no_authentication_tokens()
    {
        $this->logInfo("No authentication tokens available");
    }

    public function on_authenticate_with_user_creds()
    {
        $this->logInfo("Authenticating with user credentials");
    }

    public function on_no_authentication()
    {
        $this->logInfo("No way to authenticate");
    }
}