<?php
namespace smvc\model\sql;

class SQLiteStrategy extends StandardSQLStrategy
{
	public function tableFQN($table_name)
	{
		return $this->escapeIdentifier($table_name);
	}

	public function lastInsertId()
	{
		return 'select last_insert_rowid()';
	}

	public function setNames($charset)
	{
		return null;
	}
}