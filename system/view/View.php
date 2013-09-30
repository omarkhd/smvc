<?php

namespace smvc\view;
use Exception, SplStack, SplQueue;
use smvc\base\RequestRegistry;

class View
{
	private $name;
	private $vars;

	private $loadstack;
	private $loadqueue;

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
		$dump['vars'] = var_export($this->vars, true);
		$dump['blocks'] = $this->block->dump();
		return var_export($dump, true);
	}

	public function display(array $vars = null)
	{
		if($vars != null) {
			foreach($vars as $key => $val)
				$this->set($key, $val);
		}

		$this->loadstack = new SplStack();
		$this->loadqueue = new SplQueue();
		$this->load($this->name);
		while(!$this->loadqueue->isEmpty())
			$this->load($this->loadqueue->pop());
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

	public function set($name, $value = null)
	{
		if(is_array($name) && $value == null) {
			foreach($name as $var => $val)
				$this->vars[$var] = $val;
		}
		else
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
		return self::path($view) == null ? false : true;
	}

	public static final function path($view, $ext = 'php')
	{
		$settings = RequestRegistry::getInstance()->get('__settings__');
		foreach($settings['VIEW_DIRS'] as $lookup_path) {
			$supposed_path = sprintf('%s/%s.%s', $lookup_path, $view, $ext);
			if(file_exists($supposed_path))
				return $supposed_path;
		}
		return null;
	}

	public final function current()
	{
		return $this->loadstack->top();
	}

	protected final function inherit($view)
	{
		$this->block->register($view);
		$this->loadqueue->push($view);
	}

	private function __static__($file)
	{
		$settings = RequestRegistry::getInstance()->get('__settings__');
		$filepath = sprintf('%s/%s/%s', $settings['APPLICATION_DIR'], $settings['STATIC_DIR'], $file);
		$static_path = null;
		if(file_exists($filepath)) {
			$static_path = sprintf('/%s/%s', $settings['STATIC_DIR'], $file);
		}
		else if($settings['STATIC_DEFAULT_PATH']) {
			$static_path = sprintf('%s/%s', $settings['STATIC_DEFAULT_PATH'], $file);
		}
		else {
			$static_path = $file;
		}
		echo $static_path;
	}

	public function __call($method, array $parameters)
	{
		if($method == 'static') {
			call_user_func_array(array($this, '__static__'), $parameters);
		}
		else {
			throw new Exception(sprintf('Call to undefined method %s', $method));
		}
	}
}