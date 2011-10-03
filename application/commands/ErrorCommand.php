<?php

namespace application\commands;

class ErrorCommand extends \system\command\Command
{
	public function Execute()
	{
		$view = new \system\view\View("error");
		$view->Display();
	}
}
