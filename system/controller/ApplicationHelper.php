<?php

namespace system\controller;

class ApplicationHelper
{
	private function __construct() {}

	public static function InitRegistry(\system\base\Registry $registry = null)
	{
		//if no registry is specified, a request registry is used
		if($registry == null)
			$registry = \system\base\RequestRegistry::GetInstance();

		//now check and load configuration
		$config_dir = $_SERVER["DOCUMENT_ROOT"] . "/application/config";
		require_once $config_dir . "/databases.php";

		if(isset($databases) && is_array($databases))
			$registry->Set("databases", $databases);
	}

	public static function LoadHelper($helper)
	{
		/*
		 *	loads a helper giving relevance to the
		 *	user's defined helpers
		 */

		$st_path = $_SERVER["DOCUMENT_ROOT"] . '/application/helpers/' . $helper . '.php';
		$nd_path = $_SERVER["DOCUMENT_ROOT"] . '/system/helpers/' . $helper . '.php';
		if(file_exists($st_path))
			include_once $st_path;
		else if(file_exists($nd_path))
			include_once $nd_path;
		else
			throw new \Exception("The helper <$helper> was not found");
	}
}
