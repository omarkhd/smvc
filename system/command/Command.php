<?php

namespace system\command;

abstract class Command
{
	public $context = null;
	//public final function __construct() {}
	public abstract function execute();

	public function delegate()
	{
		return null;
	}

	public function loadHelper($helper)
	{
		$params = array();
		if(is_array($helper))
			$params = $helper;
		else
			$params = func_get_args();

		\system\controller\ApplicationHelper::loadHelper($params);
	}
}
