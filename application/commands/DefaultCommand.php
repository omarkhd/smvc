<?php

namespace application\commands;

class DefaultCommand extends \system\command\Command
{
	public function Execute()
	{
		$view = new \system\view\View("default");
		$view->Display();
	}
}
