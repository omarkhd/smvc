<?php

namespace system\view;
use Exception;

class InheritanceManager
{
	private $blocks;
	private $inherits;
	private $view;

	public function __construct(View $view)
	{
		$this->view = $view;
		$this->inherits = array();
	}

	public final function start($blockname)
	{
	}

	public final function end($blockname)
	{
	}

	public final function display($block)
	{
	}

	public final function register($inherit)
	{
		$definer = $this->view->current();
		if(isset($this->inherits[$definer]))
			throw new Exception(sprintf('Cannot redeclare inheritance for template "%s"', $definer));
		if(!View::exists($inherit)) {
			throw new Exception(sprintf('The template "%s" declared for inheritance in "%s" doesn\'t exist',
				$inherit, $definer));
		}
		$this->inherits[$definer] = $inherit;
	}
}
