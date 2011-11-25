<?php

namespace system\view;

class View
{
	private $name;
	private $vars;
	public static $debug = false;

	public function __construct($name = "index")
	{
		$this->name = $name;
	}

	public function display(array $vars = null, $html = true)
	{
		$this->vars = $vars;

		if(self::$debug)
			var_dump($vars);

		if($this->vars != null && $html)
			foreach($this->vars as $key => $val)
				$this->vars[$key] = htmlentities($val);

		$this->load($this->name);
	}

	private function load($view_name)
	{
		$vars = $this->vars;
		if($vars != null)
			foreach($vars as $var => $val)
				$$var = $val;
		
		include 'application/views/' . $view_name . '.php';
	}
}
