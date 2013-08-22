<?php
namespace smvc\controller;

class Request
{
	private $container;

	public function __construct()
	{
		$this->container = array();
		$sources = array($_FILES, $_GET, $_POST);
		foreach($sources as $source)
			foreach($source as $key => $value)
				$this->set($key, $value);
	}

	public function get($key = null)
	{
		$argc = func_num_args();
		$value = null;
		
		if($argc > 1) {
			$value = array();
			foreach(func_get_args() as $var) {
				$value[$var] = null;
				if(isset($this->container[$var]))
					$value[$var] = $this->container[$var];
			}
		}
			
		else if($key == null)
			$value = $this->container;
			
		else if(isset($this->container[$key]))
			$value = $this->container[$key];
			
		return $value;
	}
	
	public function set($key, $value)
	{
		$this->container[$key] = $value;
	}
}
