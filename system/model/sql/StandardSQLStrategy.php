<?php
namespace smvc\model\sql;

abstract class StandardSQLStrategy implements IModelSQLStrategy, IDriverSQLStrategy
{
	protected $connectionProperties;

	public function __construct(array $connection_properties)
	{
		$this->connectionProperties = $connection_properties;
	}
	/*
	 * Model's basic SQL generation strategies
	 */

	public function escapeIdentifier($identifier)
	{
		return sprintf('"%s"', $identifier);
	}

	public function escapeIdentifiers(array $identifiers)
	{
		foreach($identifiers as &$identifier)
			$identifier = $this->escapeIdentifier($identifier);
		return $identifiers;
	}

	public function getAll($table)
	{
		return sprintf('select * from %s', $this->tableFQN($table));
	}

	public function getBy($table, array $criteria)
	{
		$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
		return sprintf('select * from %s where %s', $this->tableFQN($table), $condition);
	}

	public function insert($table, array $values)
	{
		$table = $this->tableFQN($table);
		$fields = implode(', ', $this->escapeIdentifiers(array_keys($values)));
		$question_values = implode(', ', array_fill(0, count($values), '?'));
		return sprintf('insert into %s(%s) values(%s)', $table, $fields, $question_values);
	}

	public function truncate($table)
	{
		return sprintf('truncate table %s', $this->tableFQN($table));
	}

	public function deleteAll($table)
	{
		return sprintf('delete from %s', $this->tableFQN($table));
	}

	public function deleteBy($table, array $criteria)
	{
		$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
		return sprintf('delete from %s where %s', $this->tableFQN($table), $condition);
	}

	public function updateAll($table, array $values)
	{
		$values = implode(' = ?, ', $this->escapeIdentifiers(array_keys($values))) . ' = ?';
		return sprintf('update %s set %s', $this->tableFQN($table), $values);
	}

	public function updateBy($table, array $criteria, array $values)
	{
		$values = implode(' = ?, ', $this->escapeIdentifiers(array_keys($values))) . ' = ?';
		$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
		return sprintf('update %s set %s where %s', $this->tableFQN($table), $values, $condition);
	}

	public function existsBy($table, array $criteria)
	{
		$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
		return sprintf('select exists(select * from %s where %s)', $this->tableFQN($table), $condition);
	}

	public function now()
	{
		return 'select current_timestamp';
	}

	public function today()
	{
		return 'select current_date';
	}

	public function currentTime()
	{
		return 'select current_time';
	}

	private function aggregate($function, $table, $field, array $criteria)
	{
		$sql = sprintf('select %s(%s) from %s',
			$function, $field == '*' ? $field : $this->escapeIdentifier($field), $this->tableFQN($table));
		if(count($criteria) > 0) {
			$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
			$sql = sprintf('%s where %s', $sql, $condition);
		}
		return $sql;
	}

	public function count($table, $field = null, array $criteria = array())
	{
		$field = $field == null ? '*' : $field;
		return $this->aggregate('count', $table, $field, $criteria);
	}

	public function max($table, $field, array $criteria = array())
	{
		return $this->aggregate('max', $table, $field, $criteria);
	}

	public function min($table, $field, array $criteria = array())
	{
		return $this->aggregate('min', $table, $field, $criteria);
	}

	public function sum($table, $field, array $criteria = array())
	{
		return $this->aggregate('sum', $table, $field, $criteria);
	}

	public function avg($table, $field, array $criteria = array())
	{
		return $this->aggregate('avg', $table, $field, $criteria);
	}

	public function distinct($table, array $fields, array $criteria = array())
	{
		$sql = sprintf('select distinct %s from %s',
			implode(', ', $this->escapeIdentifiers($fields)), $this->tableFQN($table));
		if(count($criteria) > 0) {
			$condition = implode(' = ? and ', $this->escapeIdentifiers(array_keys($criteria))) . ' = ?';
			$sql = sprintf('%s where %s', $sql, $condition);
		}
		return $sql;
	}

	/*
	 * Driver's basic SQL generation strategies
	 */

	public function beginTransaction()
	{
		return 'start transaction';
	}

	public function commitTransaction()
	{
		return 'commit';
	}

	public function rollbackTransaction()
	{
		return 'rollback';
	}

	public function setNames($charset)
	{
		return sprintf('set names \'%s\'', $charset);
	}
}