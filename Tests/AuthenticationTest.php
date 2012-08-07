<?php

class AuthenticationTest extends PHPUnit_Framework_Testcase
{
    protected $auth;
    protected $request;

    protected function setUp()
    {
        $params = array(
            'client_id'     => '13_2s6wg16cwtk48kcgggo8kcgow44w0k8k4800ssw4oss0coc0g8',
            'client_secret' => '4htbckzh1qm8wo4s88gw44g8gs80g00so0sg0kw8kkoccco8gg'
        );
        $this->request = new Trackvia\Request();
        $this->auth = new Trackvia\Authentication($this->request, $params);

        $this->setMockTokenData();
    }

    protected function setMockTokenData($expiresAt = null)
    {
        $this->auth->setTokenData(array(
            'access_token'  => 'zH_4mT6c71qn8fD5_zIKabURN7jBj0oKvz19OUvVb5I',
            'refresh_token' => 'v5G2ZFOIx3YM0bpggN1MP_WwhVleo4zvpY2A8kEzq30',
            'expires_at'    => ($expiresAt != null ? $expiresAt : (time() + 10000))
        ));
    }

    public function testSetUserCreds()
    {
        $this->assertFalse($this->auth->hasUserCreds());

        $this->auth->setUserCreds('testuser', 'testpassword');

        $this->assertTrue($this->auth->hasUserCreds());
    }

    public function testClearAccessToken()
    {
        $this->assertTrue($this->auth->hasAccessToken());

        $this->auth->clearAccessToken();

        $this->assertFalse($this->auth->hasAccessToken());
    }

    public function testClearAllTokens()
    {
        $auth = $this->auth;
        $this->assertNotEmpty($auth->getTokenData());

        $auth->clearAllTokens();

        $this->assertEmpty($auth->getTokenData());
    }

    public function testSetTokenData()
    {
        $auth = $this->auth;
        $auth->clearAllTokens();

        // verify there is no token data first
        $this->assertEmpty($auth->getTokenData());
        $this->assertEmpty($auth->getAccessToken());
        $this->assertFalse($auth->hasAccessToken());
        $this->assertFalse($auth->hasRefreshToken());
        $this->assertEmpty($auth->getRefreshToken());

        $this->setMockTokenData();

        $this->assertNotEmpty($auth->getTokenData());
        $this->assertNotEmpty($auth->getAccessToken());
        $this->assertTrue($auth->hasAccessToken());
        $this->assertTrue($auth->hasRefreshToken());
        $this->assertNotEmpty($auth->getRefreshToken());
    }

    public function testIsAccessTokenExpired()
    {
        // expiresAt should already be set ahead from now
        $this->assertFalse($this->auth->isAccessTokenExpired());
        $this->setMockTokenData(time() - 10000);
        $this->assertTrue($this->auth->isAccessTokenExpired());
    }

    public function testAuthenticateWithExistingToken()
    {
        $this->assertTrue($this->auth->authenticate());
    }

    public function testAuthenticateWithUserCreds()
    {
        $auth =  $this->auth;
        $this->auth->clearAllTokens();

        // auth should fail with no token or user creds
        $this->assertFalse($auth->authenticate()); 
        $this->assertEmpty($auth->getTokenData());

        $this->auth->setUserCreds('api.tester', 'co3823se');
        $response = $this->auth->authenticate();

        // make sure we have some token data now
        $this->assertNotEmpty($auth->getTokenData());

        return $response;
    }

    public function testAuthenticateWithBadUserCreds($value='')
    {
        # code...
    }

    /**
     * @depends testAuthenticateWithUserCreds
     */
    public function testAuthenticateWithRefreshToken($tokenData)
    {
        $this->assertNotEmpty($tokenData['refresh_token']);
        $this->auth->setTokenData($tokenData);
        $this->auth->clearAccessToken();

        $response = $this->auth->authenticate();
        $this->assertTrue($this->auth->hasAccessToken());
    }
}