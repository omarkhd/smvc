<?php

namespace system\command;

class CommandResolver
{
	private static $defaultCommand = "Default";
	private static $errorCommand = "Error";

	public function getCommand(\system\controller\Request $request)
	{
		$cmd = $request->get("cmd");
		$classname = null;

		//generating the class name based on the expected parameters
		if($cmd == null)
			$classname = self::generateClassName(self::$defaultCommand);
		else
			$classname = self::generateClassName($cmd);

		//verifying the generated class againts the requirements
		$cmd_obj = null;
		if(class_exists($classname) && is_subclass_of($classname, '\system\command\Command'))
			$cmd_obj = new $classname();
			
		else {
			$def_class = self::generateClassName(self::$errorCommand);
			$cmd_obj = new $def_class();
		}

		echo $classname;
		$cmd_obj->context = $request;
		return $cmd_obj;
	}

	private static function generateClassName($cmd)
	{
		$parts = explode(':', $cmd); //components of the command, namespaces (:) and classname
		$cmd_part = $parts[count($parts) - 1]; //last part of the command is the class name

		//constructing the namespace
		$ns = 'application\\commands';
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
