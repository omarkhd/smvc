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
		$cmd_name = "";
		foreach(explode("_", $cmd) as $word)
			$cmd_name .= ucwords($word);
		$cmd_name .= "Command";
		$cmd_name = "\\application\\commands\\" . $cmd_name;
		return $cmd_name;
	}
}
