<?php
namespace smvc\model;
use ArrayObject;

class ResultSet extends ArrayObject
{
	public function __construct($result = array())
	{
		parent::__construct($result);
	}

	public function getFirst()
	{
		if($first = reset($this))
			return $first;
		return null;
	}

	public function valuesList($field = null)
	{
		if(count($this) == 0)
			return array();
		$field = $field == null ? reset(array_keys(reset($this))) : $field;
		$values_list = array();
		foreach($this as $tuple)
			$values_list[] = $tuple[$field];
		return $values_list;
	}
}