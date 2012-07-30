<?php 
namespace Trackvia;

class Authentication
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


	/**
	 * @param TrackviaRequst $request
	 */
	public function __construct(Request $request, $params)
	{
		$this->request = $request;

		if (!is_array($params)) {
            throw new Exception('You must pass in your client_id and client_secrect');
        }
        if (!isset($params['client_id'])) {
            throw new Exception('No client_id provided. This is required.');
        }
        if (!isset($params['client_secret'])) {
            throw new Exception('No client_secrect provided. This is required.');
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
     * @param [type] $username [description]
     * @param [type] $password [description]
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
    private function hasUserCreds()
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
        return !empty($this->tokenData) && isset($this->tokenData['access_token']);
    }

    /**
     * Get the current access token.
     * @return string
     */
    public function getAccessToken()
    {
        return $this->tokenData['access_token'];
    }

    /**
     * Check if there is a refresh token set.
     * @return boolean
     */
    public function hasRefreshToken()
    {
        return !empty($this->tokenData) && isset($this->tokenData['refresh_token']);
    }

    /**
     * Get the current refresh token.
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->tokenData['refresh_token'];
    }

    /**
     * Clear the current token data
     */
    private function clearTokenData()
    {
    	$this->tokenData = null;
    }

    /**
     * Check if the current token expired.
     * We use the expired_at time that should be set by the client.
     * @return boolean
     */
    private function isAccessTokenExpired()
    {
        return $this->tokenData['expires_at'] > time();
    }

    /**
     * Check if there is an access token and if it is expired base on the expired_at property.
     * Not a valid token if either condition fails.
     * 
     * @return boolean
     */
    private function isAccessTokenValid()
    {
    	return $this->hasAccessToken() && !$this->isAccessTokenExpired();
    }

	/**
     * Get an access token from the Trackvia OAuth2 server.
     * 
     * @param  string $username The user's username credential
     * @param  string $password The user's password credential
     * @return string The access token for the user
     */
    public function requestTokenWithUserCreds($username, $password)
    {
        $url = $this->getTokenUrl();

        $response = $this->request->post($url, array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'password',
            'username'      => $username,
            'password'      => $password
        ));

        if (!$response) {
            throw new Exception('Requesting Access Token failed');
        }

        $this->tokenData = $response;
        $this->tokenData['expires_at'] = $this->tokenData['expires_in'] + time();

        return $this->tokenData;
    }

    /**
     * Get a new access token with a refresh token.
     * 
     * @param  string $refreshToken 
     * @return array Array of token data returned from the auth server
     */
    public function requestTokenWithRefreshToken($refreshToken)
    {
        // use the refresh token to get a new access token
        $url = $this->getTokenUrl();

        $response = $this->request->post($url, array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken
        ));

        $this->tokenData = $response;
        $this->tokenData['expires_at'] = $this->tokenData['expires_in'] + time();

        return $this->tokenData;
    }

    /**
     * Authenticate the user based on what parameters have been set so far.
     * If there is no token, request one with user creds if they exist.
     * 
     * @return array Access token data
     */
    public function authenticate()
    {
    	$response = true;

    	if (!$this->isAccessTokenValid()) {
	        if (!$this->hasRefreshToken()) {
	            // no tokens available, so we need to request new ones
	            
	            // check for user credentials flow first
	            if ($this->hasUserCreds()) {
	                $response = $this->requestTokenWithUserCreds(
	                    $this->userCreds['username'],
	                    $this->userCreds['password']
	                );
	            }

	            //TODO add support for redirecting user to auth trackvia endpoint

	        } 
	        elseif ($this->hasRefreshToken()) {
	            // use the refresh token to get a new access token
	            $response = $this->requestTokenWithRefreshToken($this->getRefreshToken());

	            //TODO if the refresh token is expired we need to automatically request a new access token
	        }
    	}
    	
        //TODO check for response errors here
        

        return $response;
    }

    private function getAuthUrl()
    {
        return TrackviaApi::BASE_URL . self::AUTH_URL;
    }

    private function getTokenUrl()
    {
        return TrackviaApi::BASE_URL . self::TOKEN_URL;
    }
}