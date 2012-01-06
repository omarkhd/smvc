<?php

namespace system\controller;

class Request
{
	private $container;
	private $messages;

	public function __construct()
	{
		$this->container = $_REQUEST;
	}

	public function get($key)
	{
		if(isset($this->container[$key]))
			return $this->container[$key];
		return null;
	}

	public function addMessage($msg)
	{
		$this->messages[] = $msg;
	}

	public function getMessages()
	{
		return $this->messages;
	}
}
