<?php

namespace system\command;

class CommandResolver
{
	private static $DefaultCommand = "Default";
	private static $ErrorCommand = "Error";

	public function GetCommand(\system\controller\Request $request)
	{
		$cmd = $request->Get("cmd");
		$classname = null;

		if($cmd == null)
			$classname = self::GenerateClassName(self::$DefaultCommand);

		else
			$classname = self::GenerateClassName($cmd);

		$cmd_obj = null;
		if(@class_exists($classname) && @is_subclass_of($classname, '\system\command\Command'))
			$cmd_obj = new $classname();

		else
		{
			$def_class = self::GenerateClassName(self::$ErrorCommand);
			$cmd_obj = new $def_class();
		}

		$cmd_obj->ContextRequest = $request;
		return $cmd_obj;
	}

	private static function GenerateClassName($cmd)
	{
		$parts = explode(':', $cmd); //components of the command, namespaces (:) and classname
		$cmd_part = $parts[count($parts) - 1]; //last part of the command is the class name

		//constructing the namespace
		$ns = '\\application\\commands';
		for($i = 0; $i < count($parts) - 1; $i++)
			$ns .= '\\' . $parts[$i];
		$ns .= '\\';

		//constructing the classname
		$classname = '';
		foreach(explode("_", $cmd_part) as $word)
			$classname .= ucwords($word);
		$classname .= "Command";

		return $ns . $classname;
	}
}
