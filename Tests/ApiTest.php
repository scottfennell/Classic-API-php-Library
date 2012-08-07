<?php

require 'Trackvia/Api.php';

class ApiTest extends PHPUnit_Framework_Testcase
{
	protected $api;

	protected function setUp()
	{
		$params = array(
            'client_id'     => '13_2s6wg16cwtk48kcgggo8kcgow44w0k8k4800ssw4oss0coc0g8',
            'client_secret' => '4htbckzh1qm8wo4s88gw44g8gs80g00so0sg0kw8kkoccco8gg'
        );
		$this->api = new Trackvia\Api($params);
	}

	public function testAuthenticate()
	{
		$this->assertNotEmpty($this->api);
	}
}