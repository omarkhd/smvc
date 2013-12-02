<?php
namespace smvc\base;

/*
 * a registry for the session scope
 */
class SessionRegistry implements Registry
{
	private static $instance = null;
	
	private function __construct()
	{
		$settings = RequestRegistry::getInstance()->get('__settings__');
		if(isset($settings['SESSION_NAME']) && $settings['SESSION_NAME'])
			session_name($settings['SESSION_NAME']);
		session_start();
	}

	public function clear()
	{
		$_SESSION = array();
	}

	public static function destroy()
	{
		self::getInstance()->clear();
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