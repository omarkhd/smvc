<?php

namespace system\view;
use Exception;

class View
{
	const dir = 'application/views';
	private $name;
	private $vars;

	private $loadstack;

	protected $block;

	public function __construct($name = 'default')
	{
		$this->clear();
		$this->name = $name;
		$this->block = new InheritanceManager($this);
	}

	public function clear()
	{
		$this->vars = array();
	}

	public function dump()
	{
		return var_export($this->vars, true);
	}

	public function display(array $vars = null)
	{
		if($vars != null)
			foreach($vars as $key => $val)
				$this->set($key, $val);
		$this->loadstack = new \SplStack();
		$this->load($this->name);
	}

	private final function load($view_name)
	{
		$this->loadstack->push($view_name);

		if(!self::exists($view_name))
			throw new Exception(sprintf('Could not find view template "%s"', $view_name ));
		if($this->vars != null) {
			foreach($this->vars as $var => $val)
				$$var = $val;
		}
		require self::path($view_name);

		$this->loadstack->pop();
	}

	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function get($name)
	{
		if(isset($this->vars[$name]))
			return $this->vars[$name];
		return null;
	}

	public static final function exists($view)
	{
		return file_exists(self::path($view));
	}

	public static final function path($view, $ext = 'php')
	{
		return sprintf('%s/%s.%s', self::dir, $view, $ext);
	}

	/* block engine */

	public final function current()
	{
		return $this->loadstack->top();
	}

	protected final function inherit($view)
	{
		$this->block->register($view);
	}
}
