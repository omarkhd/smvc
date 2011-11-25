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
		$r->set('base_url', $base_url);
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

		$st_path = 'application/helpers/' . $helper . '.php';
		$nd_path = 'application/helpers/' . $helper . '.inc';
		$rd_path = 'system/helpers/' . $helper . '.php';
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
