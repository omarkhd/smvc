<?php

namespace system\view;
use Exception;

class View
{
	private $name;
	private $vars;
	private static $dir = 'application/views';

	public function __construct($name = 'default')
	{
		$this->name = $name;
		$this->clear();
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
		$this->load($this->name);
	}

	private function load($view_name)
	{
		if(!self::exists($view_name))
			throw new Exception(sprintf('Could not find view template "%s"', $view_name ));
		$vars = $this->vars;
		if($vars != null)
			foreach($vars as $var => $val)
				$$var = $val;
		require self::getPath($view_name);
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

	public static function exists($view)
	{
		return file_exists(self::getPath($view));
	}

	public static function getPath($view, $ext = 'php')
	{
		return sprintf('%s/%s.%s', self::$dir, $view, $ext);
	}
}
