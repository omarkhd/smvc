<?php

namespace smvc\model;
use Exception;

class Model
{
	protected $tableName;
	protected $idName;
	private $strategy;

	public function __construct($table_name, $id_name = "id", $connection = "default")
	{
		$dbname = DatabaseFactory::getProperty($connection, 'name');
		$this->tableName = "{$dbname}.{$table_name}";
		$this->idName = $id_name;
		$this->strategy = DatabaseFactory::getStrategy($connection);
	}

	public function getAll($start = null, $count = null)
	{
		$sql = "select * from $this";
		if(is_integer($start) && is_integer($count))
			$sql .= " limit $start, $count";
		else if(is_integer($start))
			$sql .= " limit $start";
		
		return $this->doQuery($sql);
	}

	public function get($id)
	{
		$rows = $this->getBy($this->idName, $id);
		if($rows != null)
			return $rows[0];
	}

	public function getBy($col_name, $value = true)
	{
		$params = $this->normalizeParams($col_name, $value);
		$op = (bool) $value ? 'and' : 'or';

		$condition = implode(" = ? $op ", array_keys($params)) . ' = ?';
		$sql = "select * from $this where $condition";
		return $this->doQuery($sql, array_values($params));
	}
	
	public function getUnique($field, $value)
	{
		$tuples = $this->getBy($field, $value);
		if(isset($tuples[0]))
			return $tuples[0];
		return null;
	}

	public function getAllLike(array $criterias, $empty_gets_all = true)
	{
		if(!is_array($criterias) || count($criterias) == 0)
			return $empty_gets_all ? $this->getAll() : null;

		$sql = "select * from $this where ";
		$params = array();
		foreach($criterias as $column => $criteria)
		{
			$sql .= $column . " like ? or ";
			$params[] = "%$criteria%";
		}

		$sql = substr($sql, 0, strlen($sql) - 3);
		return $this->doQuery($sql, $params);
	}

	public function insert(array $params)
	{
		/*
			this method automatically detects if the array
			is associative or numeric, if it is associative,
			it creates column names for the insert, if not, just
			puts them in order without column names
		*/
			
		$by_columns = false;
		$keys = null;
		$values = array_values($params);
		$length = count($params);

		if($this->isAssociative($params)) {
			$by_columns = true;
			$keys = array_keys($params);
		}
		
		$sql = "insert into $this ";
		$sql .= $by_columns ? '(' . implode(', ', $keys) . ') ' : '';
		$sql .= 'values (' . implode(', ', array_fill(0, $length, '?')) . ')';
		return $this->doNonQuery($sql, $values) > 0;
	}

	public function deleteBy($col_name, $value = true)
	{
		$params = $this->normalizeParams($col_name, $value);
		$op = (bool) $value ? 'and' : 'or';

		$condition = implode(" = ? $op ", array_keys($params)) . ' = ?';
		$sql = "delete from $this where $condition";

		return $this->doNonQuery($sql, array_values($params));
	}

	public function delete($value)
	{
		return $this->deleteBy($this->idName, $value) > 0;
	}

	public function deleteAll()
	{
		$sql = "delete from $this";
		return $this->doNonQuery($sql);
	}

	public function update($id, $what, $new = null)
	{
		if(is_array($what) && $this->isAssociative($what))
			return $this->updateBy(array($this->idName => $id), $what) > 0;
		return $this->updateBy($this->idName, $id, $what, $new) > 0;
	}

	#params 1) criteria_array 2) update_array 3) every condition or not
	public function updateBy($col_criteria, $criteria, $what = true, $new = null)
	{
		#for backwards compatibility
		if(!(is_array($col_criteria) || is_array($criteria))) {
			if(func_num_args() != 4)
				throw new Exception('Deprecated update need 4 parameters');
			$sql = "update $this set $what = ? where $col_criteria = ?";
			return $this->doNonQuery($sql, array($new, $criteria));
		}
		
		$condition_params = $this->normalizeParams($col_criteria, null);
		$set_params = $this->normalizeParams($criteria, null);
		$op = (bool) $what ? 'and' : 'or';

		$updates = implode(' = ?, ', array_keys($set_params)) . ' = ?';
		$conditions = implode(" = ? $op ", array_keys($condition_params)) . ' = ?';
		$sql_params = array();
		foreach(array_values($set_params) as $item) $sql_params[] = $item;
		foreach(array_values($condition_params) as $item) $sql_params[] = $item;

		$sql = "update $this set $updates where $conditions";
		return $this->doNonQuery($sql, $sql_params);
	}

	public function exists($id)
	{
		return $this->existsBy($this->idName, $id);
	}

	public function existsBy($field, $value)
	{
		$sql =	"select count(*) from $this
				where $field = ?";
		return $this->doScalar($sql, array($value)) > 0;
	}

	protected function doQuery($sql, array $params = null)
	{
		return $this->strategy->doQuery($sql, $params);
	}

	protected function doScalar($sql, array $params = null)
	{
		return $this->strategy->doScalar($sql, $params);
	}

	protected function doNonQuery($sql, array $params = null)
	{
		return $this->strategy->doNonQuery($sql, $params);
	}

	public function lastInsertId()
	{
		return $this->strategy->lastInsertId();
	}

	public function error()
	{
		$error = $this->doQuery('show errors');
		if(isset($error[0]))
			return $error[0];
		return null;
	}

	public function driver()
	{
		return $this->strategy->driver();
	}

	public function begin()
	{
		return $this->strategy->begin();
	}

	public function commit()
	{
		return $this->strategy->commit();
	}

	public function rollback()
	{
		return $this->strategy->rollback();
	}

	public function now()
	{
		return $this->doScalar('select now()');
	}

	public function today()
	{
		return $this->doScalar('select current_date');
	}

	public function count($what = null)
	{
		if($what == null)
			$what = '*';			
		return $this->doScalar("select count($what) from $this");
	}

	public function max($expr)
	{
		return $this->doScalar("select max($expr) from $this");
	}

	public function min($expr)
	{
		return $this->doScalar("select min($expr) from $this");
	}

	public function sum($expr)
	{
		return $this->doScalar("select sum($expr) from $this");
	}

	public function avg($expr)
	{
		return $this->doScalar("select avg($expr) from $this");
	}
	
	public function __toString()
	{
		return $this->tableName;
	}

	public function distinct($what)
	{
		if(is_array($what))
			$what = implode(', ', $what);
		return $this->doQuery("select distinct $what from $this");
	}

	private function isAssociative(array $to_test)
	{
		if(count($to_test) == 0)
			return false;
		foreach(array_keys($to_test) as $key)
			if(!is_integer($key))
				return true;
		return false;
	}

	private function normalizeParams($first, $second)
	{
		$params = array();
		if(!(is_array($first) || is_array($second)))
			$params[$first] = $second;
		else if(is_array($first) && $this->isAssociative($first))
			$params = $first;
		else
			throw new Exception('Cannot generate a correct query with this parameters');
		return $params;
	}

	protected function setStrategy(IDriverCoreQueryStrategy $strategy)
	{
		$this->strategy = $strategy;
	}

	public function getStrategy()
	{
		return $this->strategy;
	}

	public function useConnectionOf(Model $model)
	{
		$this->setStrategy($model->getStrategy());
	}
}
