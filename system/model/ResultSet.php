<?php
namespace smvc\model;
use ArrayObject;

class ResultSet extends ArrayObject
{
	public function __construct($result = array())
	{
		parent::__construct($result);
	}
}