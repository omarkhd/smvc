<?php

/*

a registry for the session scope

*/

namespace system\base;

class SessionRegistry extends Registry
{
	private static $Instance = null;
	
	private function __construct()
	{
		session_start();
	}

	public function Clear()
	{
		$_SESSION = array();
	}

	public static function DestroySession()
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		self::$Instance = null;
	}

	public static function GetInstance()
	{
		if(self::$Instance == null)
			self::$Instance = new SessionRegistry();

		return self::$Instance;
	}

	public function Get($key)
	{
		if(isset($_SESSION[$key]))
			return unserialize($_SESSION[$key]);

		return null;
	}

	public function Set($key, $value)
	{
		$_SESSION[$key] = serialize($value);
	}
}

?>
