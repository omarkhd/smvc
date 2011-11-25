<?php

/*

a registry for the request scope

*/

namespace system\base;

class RequestRegistry extends Registry
{
	private static $Instance = null;
	private $Container;

	private function __construct()
	{
		$this->clear();
	}

	public function clear()
	{
		$this->container = array();
	}

	public static function getInstance()
	{
		if(self::$Instance == null)
			self::$Instance = new self();

		return self::$Instance;
	}

	public function get($key)
	{
		if(isset($this->Container[$key]))
			return $this->Container[$key];

		return null;
	}

	public function set($key, $value)
	{
		$this->Container[$key] = $value;
	}
}

?>
