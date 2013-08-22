<?php
namespace smvc\controller;
use smvc\base\Registry;
use smvc\base\RequestRegistry;
use Exception;

class ApplicationHelper
{
	private function __construct() {}
	private static $ConfDir = null;

	public static function initRegistry(Registry $registry = null)
	{
		//if no registry is specified, a request registry is used
		if($registry == null)
			$registry = RequestRegistry::getInstance();

		if(self::$ConfDir == null)
			self::$ConfDir = 'application/config';

		self::loadConfig($registry);
		self::loadDatabaseConfig($registry);
	}

	private static function loadConfig(Registry $registry)
	{
		require_once self::$ConfDir . '/settings.php';
		if(isset($session_name))
			$registry->set('session_name', $session_name);
	}

	private static function loadDatabaseConfig(Registry $registry)
	{
		//now check and load configuration
		require_once self::$ConfDir . "/databases.php";
		
		if(isset($databases) && is_array($databases))
			$registry->set("databases", $databases);
	}
}
