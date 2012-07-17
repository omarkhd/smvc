<?php

namespace system\command;
use \Exception;

abstract class Command
{
	public $context = null;
	#public final function __construct() {}
	
	protected function before() {}
	protected abstract function execute();
	protected function after() {}

	public function onException(Exception $exception)
	{
		throw $exception;
	}

	public function run()
	{
		try {
			$this->before();
			$this->execute();
			$this->after();
		}
		catch(Exception $exception)
		{
			$this->onException($exception);
		}
	}

	public function delegate()
	{
		return null;
	}

	public function loadHelper($helper)
	{
		$params = array();
		if(is_array($helper))
			$params = $helper;
		else
			$params = func_get_args();

		\system\controller\ApplicationHelper::loadHelper($params);
	}
}
