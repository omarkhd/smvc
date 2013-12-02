<?php
namespace smvc\controller;
use smvc\base\Registry;

class Request implements Registry
{
	private $container;

	public function __construct()
	{
		$this->container = array();
		$sources = array($_FILES, $_GET, $_POST);
		foreach($sources as $source)
			foreach($source as $key => $value)
				$this->set($key, $value);
	}

	public function get($key = null)
	{
		return isset($this->container[$key]) ? $this->container[$key] : null;
	}
	
	public function set($key, $value)
	{
		$this->container[$key] = $value;
	}

	public function clear()
	{
		$this->container = array();
	}

	public function src()
	{
		return $this->server('REMOTE_ADDR');
	}

	public function dst()
	{
		return $this->server('SERVER_ADDR');
	}

	public function port()
	{
		return $this->server('REMOTE_PORT');
	}

	public function method()
	{
		return $this->server('REQUEST_METHOD');
	}

	public function path()
	{
		return $this->server('PHP_SELF');
	}

	public function referer()
	{
		return $this->server('HTTP_REFERER');
	}

	public function agent()
	{
		return $this->server('HTTP_USER_AGENT');
	}

	private function server($key)
	{
		return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
	}

	public function dump()
	{
		return var_export($this->container, true);
	}

	public function isGet()
	{
		return $this->method() == 'GET';
	}

	public function isPost()
	{
		return $this->method() == 'POST';
	}
}
