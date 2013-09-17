<?php
namespace smvc\model\sql;
use Exception;

class MySQLStrategy extends StandardSQLStrategy
{
	public function tableFQN($table_name)
	{
		if(!is_array($table_name))
			$table_name = array($this->connectionProperties['name'], $table_name);
		if(count($table_name) > 2)
			throw new Exception('Wrong table especification');
		foreach($table_name as &$identifier)
			$identifier = $this->escapeIdentifier($identifier);
		return implode('.', $table_name);
	}

	public function escapeIdentifier($identifier, $separator_aware = true)
	{
		return sprintf('`%s`', $identifier);
	}

	public function lastInsertId()
	{
		return 'select last_insert_id()';
	}
}