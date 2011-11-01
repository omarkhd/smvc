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
}
