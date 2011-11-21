<?php

namespace system\view;

class View
{
	private $ViewName;
	private $ViewVars;
	public static $Debug = false;

	public function __construct($name = "index")
	{
		$this->ViewName = $name;
	}

	public function Display($vars = null)
	{
		if($vars != null && !is_array($vars))
			throw new Exception("The var passed to View::Display should be an array");
		$this->ViewVars = $vars;

		if(self::$Debug)
			var_dump($vars);

		$this->Load($this->ViewName);
	}

	private function Load($view_name)
	{
		$vars = $this->ViewVars;
		if($vars != null)
			foreach($vars as $var => $val)
				$$var = $val;
		
		include $_SERVER["DOCUMENT_ROOT"] . "/application/views/" . $view_name . ".php";
	}
}
