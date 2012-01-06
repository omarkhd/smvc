<?php

/*

a registry for the session scope

*/

namespace system\base;

class SessionRegistry extends Registry
{
	private static $instance = null;
	
	private function __construct()
	{
		$r = RequestRegistry::getInstance();
		$session = $r->get('session_name');
		if(!empty($session))
			session_name($session);
			
		session_start();
	}

	public function clear()
	{
		$_SESSION = array();
	}

	public static function destroySession()
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		self::$instance = null;
	}

	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new SessionRegistry();

		return self::$instance;
	}

	public function get($key)
	{
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];

		return null;
	}

	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}
}

?>
