<?php
namespace application\models;
use smvc\model\Model;

class TestModel extends Model
{
	public function __construct()
	{
		parent::__construct('my_table', 'my_table_id', 'default');
	}
}
