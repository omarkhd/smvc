<?php
namespace smvc\command;
use smvc\controller\ApplicationHelper;
use Exception;

abstract class Command
{
	public $context = null;
	#public final function __construct() {}
	
	protected function init() {}
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
			$this->init();
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
}