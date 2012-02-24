<?php

namespace system\model;

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

	public function getById($id)
	{
		$rows = $this->getBy($this->idName, $id);
		if($rows != null)
			return $rows[0];
	}

	public function getBy($col_name, $value)
	{
		$sql = "select * from $this where $col_name = ?";
		return $this->doQuery($sql, array($value));
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

	public function insert(array $values)
	{
		/*
			this method automatically detects if the array
			is associative or numeric, if it is associative,
			it creates column names for the insert, if not, just
			puts them in order without column names
		*/
			
		$k = array();
		$v = array();
		$questions = array();
		
		foreach($values as $key => $value) {
			$k[] = $key;
			$v[] = $value;
			$questions[] = '?';
		}
		
		$by_columns = true;
		foreach($k as $column) {
			if(is_numeric($column)) {
				$by_columns = false;
				break;
			}
		}
		
		$sql = "insert into $this ";
		$sql .= $by_columns ? '(' . implode(', ', $k) . ') ' : '';
		$sql .= 'values (' . implode(', ', $questions) . ')';
		return $this->doNonQuery($sql, $v) > 0;
	}

	public function deleteBy($col_name, $value)
	{
		$sql = "delete from $this where $col_name = ?";
		return $this->doNonQuery($sql, array($value));
	}

	public function deleteById($value)
	{
		return $this->deleteBy($this->idName, $value) > 0;
	}

	public function deleteAll()
	{
		$sql = "delete from $this";
		return $this->doNonQuery($sql);
	}

	public function updateById($id, $what, $new)
	{
		return $this->updateBy($this->idName, $id, $what, $new) > 0;
	}

	public function updateBy($col_criteria, $criteria, $what, $new)
	{
		$sql = "update $this set $what = ? where $col_criteria = ?";
		return $this->doNonQuery($sql, array($new, $criteria));
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
}
