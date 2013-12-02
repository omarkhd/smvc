<?php
namespace smvc\base;

interface Registry
{
	public function get($key);
	public function set($key, $val);
	public function clear();
}