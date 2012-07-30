<?php
namespace Trackvia;

require '../Monolog/Logger.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log 
{
	private $log;
	private $subject;

	public function __construct($subject)
	{
		$this->log = new Logger('name');
		$this->log->pushHandler(new StreamHandler('/Users/coake/api.log', Logger::WARNING));

		$this->initListeners();
	}

	private function initListeners()
	{
		$subject->on('api_request', array($this, 'onApiRequest'));
	}

	public function onApiRequest($data)
	{
		var_dump($data);
		$this->log->addInfo("API Request made to url $url");
	}
}