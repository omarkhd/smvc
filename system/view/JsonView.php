<?php
namespace smvc\view;
use smvc\model\ResultSet;

class JsonView extends View
{
	public function __construct()
	{
		parent::__construct('');
	}
	
	public function display(array $more_vars = array())
	{
		foreach($more_vars as $key => $value)
			$this->set($key, $value);
		$this->headers();
		echo json_encode($this->vars);
	}

	public function set($name, $value = null)
	{
		if($value instanceof ResultSet)
			$value = $value->getArrayCopy();
		parent::set($name, $value);
	}

	private function headers()
	{
		header('Content-Type:application/json;charset=UTF-8');
	}
}
