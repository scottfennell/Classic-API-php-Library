<?php 
namespace Trackvia;

class Authentication extends EventDispatcher
{
    /**
     * Path to the OAuth2 authentication endpoint
     */
    const AUTH_TOKEN = 'oauth/v2/auth';
    
    /**
     * Path to the token endpoint
     */
    const TOKEN_URL = 'oauth/v2/token';

    /**
     * Object to handle http requests
     * @var TrackviaRequest
     */
    private $request;

    /**
     * Client Id passed in by the client app
     * @var string
     */
    private $clientId;

    /**
     * Client secret key passed in by the client app
     * @var string
     */
    private $clientSecret;

    /**
     * User credentials
     * @var array
     */
    private $userCreds;

    /**
     * Array containing any token data returned after user authentication
     * @var array
     */
    private $tokenData;

    private $isTokenExpired = false;

    private $lastError;


    /**
     * @param TrackviaRequst $request
     */
    public function __construct(Request $request, $params)
    {
        $this->request = $request;

        if (!is_array($params)) {
            throw new \Exception('You must pass in your client_id and client_secrect');
        }
        if (!isset($params['client_id'])) {
            throw new \Exception('No client_id provided. This is required.');
        }
        if (!isset($params['client_secret'])) {
            throw new \Exception('No client_secrect provided. This is required.');
        }

        $this->clientId     = $params['client_id'];
        $this->clientSecret = $params['client_secret'];

        if (isset($params['username'])) {
            // user credentials flow is being used
            $this->setUserCreds($params['username'], $params['password']);
        }
    }

    /**
     * Set the user credentials to use for authentication
     * @param string $username
     * @param string $password
     */
    public function setUserCreds($username, $password)
    {
        $this->userCreds = array(
            'username' => $username,
            'password' => $password
        );
    }

    /**
     * Whether or not user creds are provided
     * @return boolean
     */
    public function hasUserCreds()
    {
        return ( 
            !empty($this->userCreds) && 
            isset($this->userCreds['username']) &&
            isset($this->userCreds['password']) 
        );
    }

    /**
     * Set the token data to use for authentication.
     * @param array $params
     */
    public function setTokenData($params)
    {
        $this->tokenData = $params;
    }

    /**
     * Get the currently set token data
     * @return array
     */
    public function getTokenData()
    {
        return $this->tokenData;
    }

    /**
     * Check if there is an access token set.
     * @return boolean
     */
    public function hasAccessToken()
    {
        return !empty($this->tokenData) && isset($this->tokenData['access_token']) && $this->tokenData['access_token'] != '';
    }

    /**
     * Get the current access token.
     * @return string
     */
    public function getAccessToken()
    {
        return isset($this->tokenData['access_token']) ? $this->tokenData['access_token'] : null;
    }

    /**
     * Check if there is a refresh token set.
     * @return boolean
     */
    public function hasRefreshToken()
    {
        if( !empty($this->tokenData) && isset($this->tokenData['refresh_token']) && $this->tokenData['refresh_token']) {
            $retval = true;
            $token = $this->tokenData['refresh_token'];
        } else {
            $retval = false;
            $token = null;
        }

        $this->trigger('has_refresh_token', array('refresh_token' => $token));

        return $retval;
    }

    /**
     * Get the current refresh token.
     * @return string
     */
    public function getRefreshToken()
    {
        return isset($this->tokenData['refresh_token']) ? $this->tokenData['refresh_token'] : null;
    }

    public function getExpiresAt()
    {
        return $this->tokenData['expires_at'];
    }

    /**
     * Clear the current access token by setting it to null.
     * Clearing the access token before calling the "authenticate" method
     * will force it to get a new access token.
     */
    public function clearAccessToken()
    {
        if ($this->hasAccessToken()) {
            $this->tokenData['access_token'] = null;
        }
    }

    /**
     * Clear access and refresh tokens so that authentication will request a new token
     */
    public function clearAllTokens()
    {
        $this->tokenData = array();
    }

    /**
     * Check if the current token expired.
     * We check the expired_at time that should be set by the client.
     * @return boolean
     */
    public function isAccessTokenExpired()
    {
        if (!isset($this->tokenData['expires_at']) || $this->tokenData['expires_at'] <= time()) {
            return true;
        }
        return false;
    }

    /**
     * Check if there is an access token and if it is expired based on the expired_at property.
     * Not a valid token if either condition fails.
     * 
     * @return boolean
     */
    public function isAccessTokenValid()
    {
        $retval = $this->hasAccessToken() && !$this->isAccessTokenExpired();
        $this->trigger('is_token_valid', array('is_valid' => $retval));
        return $retval;
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
        $httpCode = $this->request->getResponseCode();

        if (!$response) {
            throw new \Exception('Requesting Access Token failed');

            return false;
        }

        if ($httpCode == 400 && isset($response['error'])) {
            // throw an Exception with the returned error message
            $msg = isset($response['error_description']) ? $response['error_description'] : $response['error'];
            throw new \Exception($msg);

            return false;
        }

        return true;
    }

    /**
     * Get an access token from the Trackvia OAuth2 server.
     * 
     * @param  string $username The user's username credential
     * @param  string $password The user's password credential
     * @return array|boolean Array of token data returned from the auth server or false on error
     */
    public function requestTokenWithUserCreds($username, $password)
    {
        $this->trigger('request_token_with_user_creds', array(
            'username' => $username,
            'password' => $password
        ));

        $url = $this->getTokenUrl();

        $this->request
            ->setMethod('post')
            ->setData(array(
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'password',
                'username'      => $username,
                'password'      => $password
            ))
            ->send($url);

        $valid = $this->checkResponse();

        if ($valid) {
            $this->tokenData = $this->request->getResponse();
            $this->tokenData['expires_at'] = $this->tokenData['expires_in'] + time();

            $this->trigger('new_access_token', $this->tokenData);

            return $this->tokenData;
        }

        return false;
    }

    /**
     * Get a new access token with a refresh token.
     * 
     * @param  string $refreshToken 
     * @return array|boolean Array of token data returned from the auth server or false on error
     */
    public function requestTokenWithRefreshToken($refreshToken)
    {
        $this->trigger('request_token_with_refresh_token', array(
            'refresh_token' => $refreshToken
        ));

        // use the refresh token to get a new access token
        $url = $this->getTokenUrl();

        $this->request
            ->setMethod('post')
            ->setData(array(
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken
            ))
            ->send($url);

        $valid = $this->checkResponse();

        if ($valid) {
            $this->tokenData = $this->request->getResponse();
            $this->tokenData['expires_at'] = $this->tokenData['expires_in'] + time();

            $this->trigger('new_access_token', $this->tokenData);

            return $this->tokenData;
        }

        return false;
    }

    /**
     * Authenticate the user based on what parameters have been set so far.
     * If there is no token, request one with user creds if they exist.
     * 
     * @return array Access token data
     */
    public function authenticate()
    {
        $response = false;
        
        if ($this->isAccessTokenValid()) {
            $response = true;
        } else {
            if (!$this->hasRefreshToken()) {
                // no tokens available, so we need to request new ones
                $this->trigger('no_authentication_tokens');
                
                // check for user credentials flow first
                if ($this->hasUserCreds()) {
                    $this->trigger('authenticate_with_user_creds');
                    $response = $this->requestTokenWithUserCreds(
                        $this->userCreds['username'],
                        $this->userCreds['password']
                    );
                } else {
                    $this->trigger('no_authentication');
                }

                //TODO add support for redirecting user to auth trackvia endpoint
            } 
            else {
                // use the refresh token to get a new access token
                try {
                    $response = $this->requestTokenWithRefreshToken($this->getRefreshToken());
                } 
                catch (\Exception $e) {
                    switch ($e->getMessage()) {
                        case Api::EXPIRED_REFRESH_TOKEN:
                            $this->trigger('refresh_token_expired');
                            // Refresh token is expired so fallback to another method
                            $this->clearAllTokens();
                            $this->authenticate();
                            break;
                    }

                    // just throw the original Exception for any other errors
                    throw $e;
                }
            }
        }

        return $response;
    }

    public function getAuthUrl()
    {
        return Api::BASE_URL . self::AUTH_URL;
    }

    public function getTokenUrl()
    {
        return Api::BASE_URL . self::TOKEN_URL;
    }
}