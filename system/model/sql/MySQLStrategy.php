<?php
namespace smvc\model\sql;

class MySQLStrategy extends StandardSQLStrategy
{
	public function __construct($connection_properties)
	{
	}

	public function tableFQN($table_name)
	{
	}

	public function inTransaction()
	{
	}

	public function setNames($charset)
	{
		return sprintf('set names \'%s\'', $charset);
	}
}