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

	public function loadHelper($helper_name)
	{
		\system\controller\ApplicationHelper::loadHelper($helper_name);
	}
}
