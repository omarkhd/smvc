<?php
namespace application\commands;

class DefaultCommand extends \smvc\command\Command
{
	public function execute()
	{
		$view = new \smvc\view\View('default');
		$view->display();
	}
}