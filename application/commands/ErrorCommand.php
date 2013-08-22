<?php
namespace application\commands;

class ErrorCommand extends \smvc\command\Command
{
	public function execute()
	{
		$view = new \smvc\view\View('error');
		$view->display();
	}
}
