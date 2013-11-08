<?php
/*
 * author Omar Martín <omarkhd.mx@gmail.com>
 */

namespace smvc\model;
use smvc\model\driver\IDriverStrategy;
use Exception;

class Model
{
	protected $tableName;
	protected $pkName;
	/*
	 * @var \smvc\model\driver\IDriverStratey Strategy to execute queries and basic transaction support
	 */
	private $driver;
	/*
	 * @var \smvc\model\sql\IModelSQLStrategy Strategy used to generate the model's sql queries
	 */
	private $sql;
	public $model;

	public function __construct($table_name, $pk_name = 'id', $connection = 'default')
	{
		$this->tableName = $table_name;
		$this->pkName = $pk_name;
		$this->setDriverStrategy(DatabaseFactory::getDriverStrategy($connection));
		$this->setSQLStrategy(DatabaseFactory::getSQLStrategy($connection));
		$this->model = new SubmodelManager($this);
	}

	public function getPkName()
	{
		return $this->pkName;
	}

	public function setDriverStrategy(driver\IDriverStrategy $strategy)
	{
		$this->driver = $strategy;
	}

	public function getDriverStrategy()
	{
		return $this->driver;
	}

	protected function setSQLStrategy(sql\IModelSQLStrategy $strategy)
	{
		$this->sql = $strategy;
	}

	protected function doQuery($sql, array $params = array())
	{
		return $this->driver->doQuery($sql, $params);
	}

	protected function doScalar($sql, array $params = array())
	{
		return $this->driver->doScalar($sql, $params);
	}

	protected function doNonQuery($sql, array $params = array())
	{
		return $this->driver->doNonQuery($sql, $params);
	}

	public function getAll()
	{
		$sql = $this->sql->getAll($this->tableName);
		return $this->doQuery($sql);
	}

	public function getBy(array $criteria)
	{
		$sql = $this->sql->getBy($this->tableName, $criteria);
		return $this->doQuery($sql, array_values($criteria));
	}

	public function get($pk)
	{
		$tuples = $this->getBy(array($this->pkName => $pk));
		return $tuples->getFirst();
	}

	public function getUnique(array $criteria)
	{
		$tuples = $this->getBy($criteria);
		return $tuples->getFirst();
	}

	public function insert(array $values)
	{
		$sql = $this->sql->insert($this->tableName, $values);
		return $this->doNonQuery($sql, array_values($values)) > 0;
	}

	public function truncate()
	{
		return $this->doNonQuery($this->sql->truncate($this->tableName));
	}

	public function deleteAll()
	{
		return $this->doNonQuery($this->sql->deleteAll($this->tableName));
	}

	public function deleteBy(array $criteria)
	{
		$sql = $this->sql->deleteBy($this->tableName, $criteria);
		return $this->doNonQuery($sql, array_values($criteria));
	}

	public function delete($pk)
	{
		return $this->deleteBy(array($this->pkName => $pk)) > 0;
	}

	public function updateAll(array $values)
	{
		$sql = $this->sql->updateAll($this->tableName, $values);
		return $this->doNonQuery($sql, array_values($values));
	}

	public function updateBy(array $criteria, array $values)
	{
		$sql_params = array();
		foreach(array_values($values) as $item) $sql_params[] = $item;
		foreach(array_values($criteria) as $item) $sql_params[] = $item;
		$sql = $this->sql->updateBy($this->tableName, $criteria, $values);
		return $this->doNonQuery($sql, $sql_params);
	}

	public function update($pk, array $values)
	{
		return $this->updateBy(array($this->pkName => $pk), $values) > 0;
	}

	public function existsBy(array $criteria)
	{
		$sql = $this->sql->existsBy($this->tableName, $criteria);
		return (bool) $this->doScalar($sql, array_values($criteria));
	}

	public function exists($pk)
	{
		return $this->existsBy(array($this->pkName => $pk));
	}

	public function lastInsertId()
	{
		return $this->doScalar($this->sql->lastInsertId($this));
	}

	public function driver()
	{
		return $this->driver->driver();
	}

	public function begin()
	{
		return $this->driver->beginTransaction();
	}

	public function commit()
	{
		return $this->driver->commitTransaction();
	}

	public function rollback()
	{
		return $this->driver->rollbackTransaction();
	}

	public function now()
	{
		return $this->doScalar($this->sql->now());
	}

	public function today()
	{
		return $this->doScalar($this->sql->today());
	}

	public function currentTime()
	{
		return $this->doScalar($this->sql->currentTime());
	}

	public function count($field = null, array $criteria = array())
	{
		$sql = $this->sql->count($this->tableName, $field, $criteria);
		return $this->doScalar($sql, array_values($criteria));
	}

	public function max($field, array $criteria = array())
	{
		$sql = $this->sql->max($this->tableName, $field, $criteria);
		return $this->doScalar($sql, array_values($criteria));
	}

	public function min($field, array $criteria = array())
	{
		$sql = $this->sql->min($this->tableName, $field, $criteria);
		return $this->doScalar($sql, array_values($criteria));
	}

	public function sum($field, array $criteria = array())
	{
		$sql = $this->sql->sum($this->tableName, $field, $criteria);
		return $this->doScalar($sql, array_values($criteria));
	}

	public function avg($field, array $criteria = array())
	{
		$sql = $this->sql->avg($this->tableName, $field, $criteria);
		return $this->doScalar($sql, array_values($criteria));
	}

	public function distinct(array $fields, array $criteria = array())
	{
		$sql = $this->sql->distinct($this->tableName, $fields, $criteria);
		return $this->doQuery($sql, array_values($criteria));
	}

	public function __toString()
	{
		return (string) $this->sql->tableFQN($this->tableName);
	}
}
