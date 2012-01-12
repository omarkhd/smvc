<?php

namespace system\view;

class View
{
	private $name;
	private $vars;
	public static $debug = false;
	private static $dir = 'application/views';

	public function __construct($name = "default")
	
	{
		$this->name = $name;
		$this->vars = array();
	}

	public function display(array $vars = null)
	{
		if($vars != null)
			foreach($vars as $key => $val)
				$this->set($key, $val);
		//$this->vars = $vars;

		if(self::$debug)
			var_dump($vars);

		$this->load($this->name);
	}

	private function load($view_name)
	{
		if(!self::exists($view_name))
			throw new \Exception('Could not find view template "' . $view_name . '"');

		$vars = $this->vars;
		if($vars != null)
			foreach($vars as $var => $val)
				$$var = $val;
		
		include self::getPath($view_name);
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

	public static function getPath($view, $ext = '.php')
	{
		return self::$dir . '/' . $view . $ext;
	}

	private function v($var)
	{
		if($var == '*')
			return $this->vars;

		if(isset($this->vars[$var]))
			return $this->vars[$var];

		return null;
	}

	private function e($var)
	{
		echo $this->v($var);
	}
}
