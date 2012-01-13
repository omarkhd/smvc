<?php

namespace system\command;

class CommandResolver
{
	private static $defaultCommand = "Default";
	private static $errorCommand = "Error";

	public function getCommand(\system\controller\Request $request)
	{
		$cmd = $request->get('cmd');
		$classname = null;

		//generating the class name based on the expected parameters
		if($cmd == null)
			$classname = self::generateClassName(self::$defaultCommand);
		else
			$classname = self::generateClassName($cmd);

		//verifying the generated class againts the requirements
		/*$cmd_instance = null;
		if(class_exists($classname) && is_subclass_of($classname, '\system\command\Command'))
			$cmd_instance = new $classname();*/

		$instance = self::instanceCommand($classname);
			
		//else {
		if($instance == null || empty($instance)) {
			$def_class = self::generateClassName(self::$errorCommand);
			$instance = new $def_class();
		}

		//configuring last valid instance
		$last_valid_instance = $instance;
		$last_valid_instance->context = $request;

		//checking for delegated commands
		while(($last_try = self::getDelegate($last_valid_instance)) != null) {
			$last_valid_instance = $last_try;
			$last_valid_instance->context = $request;
		}
			
		return $last_valid_instance;
	}

	private static function instanceCommand($classname)
	{
		$reflection = new \ReflectionClass($classname);
		$base_class = new \ReflectionClass('system\command\Command');
		$class = $reflection->getName();

		if(!$reflection->isSubclassOf($base_class->getName()))
			throw new \Exception("$class is not a subclass of " . $base_class->getName());

		if(!$reflection->isInstantiable())
			throw new \Exception("$class is not instantiable");

		$constructor = $reflection->getConstructor();
		if($constructor != null) {
			if($constructor->getNumberOfRequiredParameters() > 0)
				throw new \Exception("$class doesn't have a parameterless constructor");

			if(!$constructor->isPublic())
				throw new \Exception("$class's constructor is not accesible");
		}

		return $reflection->newInstance();
	}

	private static function getDelegate(Command $instance)
	{
		$o = $instance->delegate();
		if($o == null || empty($o) || !($o instanceof Command))
			return null;

		return $o;
	}

	private static function generateClassName($cmd)
	{
		$parts = explode(':', $cmd); //components of the command path, namespace (:) and classname
		$cmd_part = $parts[count($parts) - 1]; //last item of the command path is the command class name

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
