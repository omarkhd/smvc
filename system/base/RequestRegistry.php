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
		$this->Clear();
	}

	public function Clear()
	{
		$this->Container = array();
	}

	public static function GetInstance()
	{
		if(self::$Instance == null)
			self::$Instance = new self();

		return self::$Instance;
	}

	public function Get($key)
	{
		if(isset($this->Container[$key]))
			return $this->Container[$key];

		return null;
	}

	public function Set($key, $value)
	{
		$this->Container[$key] = $value;
	}
}

?>
