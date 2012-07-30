<?php 
namespace Trackvia;

class Request
{
	private $curl;

	private $accessToken;

	private $isTokenExpired = false;

	public function __construct()
	{
		$this->curl = curl_init();
	}

	public function setReturnType($type)
	{
		# code...
	}

	public function request($url, $httpMethod = 'POST', $data = null)
	{
		if ( !in_array($httpMethod, array('POST', 'GET', 'PUT', 'DELETE')) ) {
			throw new Exception('Request type "' . $httpMethod . '" not supported');
		}
		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
		if (is_array($data) && count($data) > 0) {
			// set any post data
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);
		$response = json_decode($response, true);

		if (isset($response['error'])) {
			$this->parseError($reponse);
			return false;
		}
		return $response;
	}

	/**
	 * GET request.
	 * Additional data will be appended to the URL.
	 * 
	 * @param  string $url
	 * @param  array $data Data to add on to the request
	 * @return array
	 */
	public function get($url, $data)
	{
		$url = $url . '?' . http_build_query($data);
		return $this->request($url, 'GET');
	}

	/**
	 * POST request
	 * 
	 * @param  string $url
	 * @param  aray $data POST fields to send with request
	 * @return array
	 */
	public function post($url, $data)
	{
		return $this->request($url, 'POST', $data);
	}

	/**
	 * PUT request
	 * 
	 * @param  string $url
	 * @param  aray $data Data fields to send with request
	 * @return array
	 */
	public function put($url, $data)
	{
		return $this->request($url, 'PUT', $data);
	}

	/**
	 * DELETE request
	 * 
	 * @param  string $url
	 * @param  aray $data Data fields to send with request
	 * @return array
	 */
	public function delete($url, $data)
	{
		return $this->request($url, 'DELETE', $data);
	}

	private function parseError($data)
	{
		switch ($data['error_description']) {
			case self::ERROR_EXPIRED_TOKEN:
				$this->isTokenExpired = true;
				// return here so we don't throw this error
				// so we can use the refresh token
				return;
		}

		// throw an exception with the returned error message
		throw new Exception($data['error_description']);
	}

	/**
	 * Repeat the last
	 * @return [type] [description]
	 */
	public function repeatRequest()
	{
		
	}

	public function isTokenExpired()
	{
		return $this->isTokenExpired;
	}
}