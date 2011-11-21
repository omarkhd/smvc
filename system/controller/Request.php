<?php

namespace system\controller;

class Request
{
	private $Container;
	private $Messages;

	public function __construct()
	{
		$this->Container = $_REQUEST;
	}

	public function Get($key)
	{
		if(isset($this->Container[$key]))
			return $this->Container[$key];
		return null;
	}

	public function AddMessage($msg)
	{
		$this->Messages[] = $msg;
	}

	public function GetMessages()
	{
		return $this->Messages;
	}
}
