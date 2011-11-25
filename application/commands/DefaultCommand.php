<?php

namespace application\commands;

class DefaultCommand extends \system\command\Command
{
	public function execute()
	{
		$view = new \system\view\View("default");
		$view->display();
	}
}
