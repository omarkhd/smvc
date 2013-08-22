<?php
namespace smvc\base;

abstract class Registry
{
	public abstract function get($key);
	public abstract function set($key, $val);
	public abstract function clear();
}