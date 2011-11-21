<?php
/*
	actualmente todas los métodos utilizados están hechos para mysql
	por falta de tiempo
	
	aquí no se revisa por sql injection ni nada de eso, son algunas
	consultas generadas automáticamente, los checks de inputs deben realizarse
	en el controlador(comando) correspondiente
*/

namespace system\model;

abstract class Model
{
	protected $TableName;
	protected $IdName;
	protected $Db;

	public function __construct($table_name, $id_name = "Id", $connection = "default")
	{
		$this->TableName = $table_name;
		$this->IdName = $id_name;
		$this->Db = SystemPDO::GetInstance($connection);
	}

	public function GetAll($start = null, $count = null)
	{
		$sql = "select * from $this->TableName";
		if(is_integer($start) && is_integer($count))
			$sql .= " limit $start, $count";
		
		return $this->DoQuery($sql);
	}

	public function GetById($id)
	{
		$rows = $this->GetBy($this->IdName, $id);
		if(count($rows) > 0)
			return $rows[0];
		return null;
	}

	public function GetBy($col_name, $value)
	{
		$sql = "select * from $this->TableName where $col_name = ?";
		return $this->DoQuery($sql, array($value));
	}

	public function GetAllLike($criterias, $empty_gets_all = true)
	{
		if(!is_array($criterias) || count($criterias) == 0)
			return $empty_gets_all ? $this->GetAll() : null;

		$sql = "select * from $this->TableName where ";
		$params = array();
		foreach($criterias as $column => $criteria)
		{
			$sql .= $column . " like ? or ";
			$params[] = "%$criteria%";
		}

		$sql = substr($sql, 0, strlen($sql) - 3);
		return $this->DoQuery($sql, $params);
	}

	public function Insert(array $values)
	{
		if(!is_array($values))
			return false;

		$sql = "insert into $this->TableName values (";
		for($i = 0; $i < count($values); $sql .= "?, ", $i++);
		$sql = substr($sql, 0, strlen($sql) - 2) . ")";
		return $this->DoNonQuery($sql, $values) > 0;
	}

	public function DeleteBy($col_name, $value)
	{
		$sql = "delete from $this->TableName where $col_name = ?";
		return $this->DoNonQuery($sql, array($value));
	}

	public function DeleteById($value)
	{
		return $this->DeleteBy($this->IdName, $value) > 0;
	}

	public function DeleteAll()
	{
		$sql = "delete from $this->TableName";
		return $this->DoNonQuery($sql);
	}

	public function UpdateById($id, $what, $new)
	{
		return $this->UpdateBy($this->IdName, $id, $what, $new) > 0;
	}

	public function UpdateBy($col_criteria, $criteria, $what, $new)
	{
		$sql = "update $this->TableName set $what = ? where $col_criteria = ?";
		return $this->DoNonQuery($sql, array($new, $criteria));
	}

	protected function DoQuery($sql, array $params = null)
	{
		$statement = $this->Db->prepare($sql);
		$this->SetParameters($statement, $params);
		$statement->execute();
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	protected function DoScalar($sql, array $params = null)
	{
		$statement = $this->Db->prepare($sql);
		$this->SetParameters($statement, $params);
		$statement->execute();
		$record = $statement->fetchAll(\PDO::FETCH_NUM);
		return $record[0][0];
	}

	protected function DoNonQuery($sql, array $params = null)
	{
		$statement = $this->Db->prepare($sql);
		$this->SetParameters($statement, $params);
		$statement->execute();
		return $statement->rowCount();
	}

	protected function SetParameters(\PDOStatement $statement, array $params =  null)
	{
		if(!is_array($params))
			return;

		for($i = 0; $i < count($params); $i++)
			$statement->bindParam($i + 1, $params[$i]);
	}

	public function __get($property)
	{
		$return = null;
		switch($property)
		{
			case "LastInsertId":
				$return = $this->Db->lastInsertId();
				break;

			case "Count":
				$return = $this->_count();
				break;

			case "ErrorInfo":
				$return = $this->Db->errorInfo();
				break;

			case "Version":
				$return = $this->_version();
				break;

		}

		return $return;
	}

	private function _version()
	{
		return $this->DoScalar("select version()");
	}

	private function _count()
	{
		return $this->DoScalar("select count(*) from $this->TableName");
	}
}
