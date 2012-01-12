<?php

namespace system\model;

class Model
{
	protected $tableName;
	protected $idName;
	protected $db;

	public function __construct($table_name, $id_name = "Id", $connection = "default")
	{
		$dbname = DatabaseFactory::getProperty($connection, 'name');
		$this->tableName = "{$dbname}.{$table_name}";
		$this->idName = $id_name;
		$this->db = DatabaseFactory::getInstance($connection);
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
		$sql = "insert into $this values (";
		for($i = 0; $i < count($values); $sql .= "?, ", $i++);
		$sql = substr($sql, 0, strlen($sql) - 2) . ")";
		return $this->doNonQuery($sql, $values) > 0;
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
		$statement = $this->db->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		$set = $statement->fetchAll(\PDO::FETCH_ASSOC);
		if(count($set) > 0)
			return $set;
		return null;
	}

	protected function doScalar($sql, array $params = null)
	{
		$statement = $this->db->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		$record = $statement->fetchAll(\PDO::FETCH_NUM);
		if(count($record) > 0)
			return $record[0][0];
		return null;
	}

	protected function doNonQuery($sql, array $params = null)
	{
		$statement = $this->db->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		return $statement->rowCount();
	}

	protected function setParameters(\PDOStatement $statement, array $params =  null)
	{
		if(!is_array($params))
			return;

		for($i = 0; $i < count($params); $i++)
			$statement->bindParam($i + 1, $params[$i]);
	}

	public function lastInsertId()
	{
		return $this->db->lastInsertId();
	}

	public function error()
	{
		return $this->db->errorInfo();
	}

	public function version()
	{
		return $this->doScalar('select version()');
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
