<?php

namespace system\command;

abstract class Command
{
	public $ContextRequest = null;
	public final function __construct() {}
	public abstract function Execute();

	public function LoadHelper($helper_name)
	{
		$dir = $_SERVER["DOCUMENT_ROOT"] . "/application/helpers";
		$file_name = $dir . "/{$helper_name}.php";
		if(file_exists($file_name))
			include $file_name;
		else
			throw new \Exception("El helper $helper_name no existe");
	}
}
