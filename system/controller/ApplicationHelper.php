<?php

namespace system\controller;

class ApplicationHelper
{
	private function __construct() {}
	private static $ConfDir = null;

	public static function initRegistry(\system\base\Registry $registry = null)
	{
		//if no registry is specified, a request registry is used
		if($registry == null)
			$registry = \system\base\RequestRegistry::getInstance();

		if(self::$ConfDir == null)
			self::$ConfDir = 'application/config';

		self::loadConfig($registry);
		self::loadDatabaseConfig($registry);
	}

	private static function loadConfig(\system\base\Registry $r)
	{
		require_once self::$ConfDir . '/settings.php';
		$r->set('session_name', $session_name);
	}

	private static function loadDatabaseConfig(\system\base\Registry $r)
	{
		//now check and load configuration
		require_once self::$ConfDir . "/databases.php";
		
		if(isset($databases) && is_array($databases))
			$r->set("databases", $databases);
	}

	public static function loadHelper($helper)
	{
		/*
		 *	loads a helper giving relevance to the
		 *	user's defined helpers
		 */

		$helpers = array();
		if(is_array($helper))
			$helpers = $helper;
		else
			$helpers = func_get_args();

		foreach($helpers as $h) {
			$st_path = 'application/helpers/' . $h . '.php';
			$nd_path = 'application/helpers/' . $h . '.inc';
			$rd_path = 'system/helpers/' . $h . '.php';
			if(file_exists($st_path))
				include_once $st_path;
			else if(file_exists($nd_path))
				include_once $nd_path;
			else if(file_exists($rd_path))
				include_once $rd_path;
			else
				throw new \Exception("The helper [$helper] was not found");
		}
	}
}
