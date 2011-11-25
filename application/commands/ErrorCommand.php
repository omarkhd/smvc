<?php

namespace application\commands;

class ErrorCommand extends \system\command\Command
{
	public function execute()
	{
		$view = new \system\view\View("error");
		$view->display();
	}
}
