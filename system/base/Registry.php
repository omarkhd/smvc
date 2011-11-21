<?php

namespace system\base;

abstract class Registry
{
	public abstract function Get($key);
	public abstract function Set($key, $val);
	public abstract function Clear();
}

?>
