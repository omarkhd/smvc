<?php

namespace smvc\view;

class Block
{
	public $name;
	public $content;
	public $master;
	public $definer;
	public $size;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function display()
	{
		echo $this->content;
	}
}
