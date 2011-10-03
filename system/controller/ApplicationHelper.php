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

		//now load the config files
		
		$config_dir = $_SERVER["DOCUMENT_ROOT"] . "/application/config";
		//$config_dir = "config";
		include_once $config_dir . "/options.php";

		foreach($OPTIONS as $option => $value)
			$registry->Set($option, $value);
	}
}
