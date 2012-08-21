<?php

namespace system\view;
use Exception;
use SplStack;

class InheritanceManager
{
	private $blockstack;
	private $blocks;
	private $inherits;
	private $view;

	public function __construct(View $view)
	{
		$this->view = $view;
		$this->inherits = array();
		$this->blockstack = new SplStack;
		$this->blocks = array();
	}

	public final function start($blockname)
	{
		$this->checkEnvironment();

		$block = new Block($blockname);
		$block->definer = $this->view->current();
		$block->master = $this->inherits[$block->definer];
		$this->blockstack->push($block);
		ob_start();
	}

	public final function end($blockname)
	{
		$this->checkEnvironment();

		if($this->blockstack->count() == 0)
			throw new Exception(sprintf('Trying to close "%s" and there are no opened blocks', $blockname));

		$block = $this->blockstack->pop();
		$definer = $this->view->current();
		if($block->name != $blockname || $block->definer != $definer) {
			throw new Exception(sprintf('Block to be closed is "%s" from template "%s", not "%s" from template "%s"',
				$block->name, $block->definer, $blockname, $definer));
		}

		$block->size = ob_get_length();
		$block->content = ob_get_contents();
		$this->blocks[] = $block;
		ob_end_clean();
	}

	public final function display($blockname)
	{
		$master = $this->view->current();
		foreach($this->blocks as $block) {
			if($block->master = $master && $block->name == $blockname)
				$block->display();
		}
	}

	public final function dump()
	{
		return var_export($this->blocks, true);
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

	private function checkEnvironment()
	{
		$definer = $this->view->current();
		if(!isset($this->inherits[$definer]))
			throw new Exception(sprintf('Base template has not been defined for %s', $definer));
	}
}
