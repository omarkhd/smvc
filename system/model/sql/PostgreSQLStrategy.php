<?php
namespace smvc\model\sql;
use Exception;

class PostgreSQLStrategy extends StandardSQLStrategy
{
	public function tableFQN($table_name)
	{
		if(!is_array($table_name))
			$table_name = array($this->connectionProperties['name'], 'public', $table_name);
		if(count($table_name) == 2)
			array_unshift($table_name, $this->connectionProperties['name']);
		if(count($table_name) > 3)
			throw new Exception('Wrong table especification');
		foreach($table_name as &$identifier)
			$identifier = $this->escapeIdentifier($identifier);
		return implode('.', $table_name);
	}

	public function escapeIdentifier($identifier, $separator_aware = true)
	{
		return sprintf('"%s"', $identifier);
	}

	public function lastInsertId($model = null)
	{
		return sprintf('select currval(pg_get_serial_sequence(\'%s\', \'%s\'))', $model, $model->getPkName());
	}
}