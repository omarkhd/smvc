<?php

namespace system\view;

class View
{
	private $ViewName;

	public function __construct($name = "index")
	{
		$this->ViewName = $name;
	}

	public function Display($vars = null)
	{
		if($vars != null && !is_array($vars))
			throw new Exception("The var passed to View::Display should be an array");

		if($vars != null)
			foreach($vars as $var => $val)
				$$var = $val;

		include $_SERVER["DOCUMENT_ROOT"] . "/application/views/" . $this->ViewName . ".php";
	}

	private function Load($view_name)
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/application/views/" . $view_name . ".php";
	}
}
