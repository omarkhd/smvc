<?php

namespace system\command;

abstract class Command
{
	public $ContextRequest = null;
	public final function __construct() {}
	public abstract function Execute();

	public function LoadHelper($helper_name)
	{
		\system\controller\ApplicationHelper::LoadHelper($helper_name);
	}
}
