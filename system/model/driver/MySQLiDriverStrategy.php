<?php
namespace smvc\model\driver;
use mysqli, mysqli_stmt, Exception;
use smvc\model\ResultSet;
use smvc\model\sql\IDriverSQLStrategy;

class MySQLiDriverStrategy implements IDriverStrategy
{
	private $link = null;
	private $sqlStrategy = null;
	
	public function __construct(array $connection_properties, IDriverSQLStrategy $sql_strategy)
	{
		$this->sqlStrategy = $sql_strategy;
		$this->link = new mysqli($connection_properties['host'], $connection_properties['user'],
			$connection_properties['password'], $connection_properties['name']);
		if(mysqli_connect_error())
			throw new Exception(mysqli_connect_error());
		$this->doNonQuery($this->sqlStrategy->setNames($connection_properties['set_names']));
	}
	
	public function doQuery($sql, array $params = array())
	{
		$statement = $this->link->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		//mysqli is really fucking annoying
		$result_set = array(); // stores the entire set
		$placeholder = array(); // temporarily stores every fetched row
		$func_params = array(); // stores references to the placeholder
		$metadata = $statement->result_metadata(); 
		while($column_metadata = $metadata->fetch_field())
			$func_params[] = &$placeholder[$column_metadata->name];
		call_user_func_array(array($statement, 'bind_result'), $func_params);
		while($statement->fetch()) {
			$row = array();
			foreach($placeholder as $column => $value)
				$row[$column] = $value;
			$result_set[] = $row;
		}
		return new ResultSet($result_set);
	}

	public function doNonQuery($sql, array $params = array())
	{
		$statement = $this->link->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		return $statement->affected_rows;
	}

	public function doScalar($sql, array $params = array())
	{
		$statement = $this->link->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		//mysqli is really fucking annoying
		$placeholder = array(); // temporarily stores the first fetched row
		$func_params = array(); // stores references to the placeholder
		$metadata = $statement->result_metadata(); 
		while($column_metadata = $metadata->fetch_field())
			$func_params[] = &$placeholder[$column_metadata->name];
		call_user_func_array(array($statement, 'bind_result'), $func_params);
		if($statement->fetch())
			return reset($placeholder);
			#foreach($placeholder as $value)
			#	return $value; //returning only the first value found in the result set
		return null; //or return null
	}

	public function lastInsertId()
	{
		return $this->doScalar('select last_insert_id()');
	}

	public function beginTransaction()
	{
		$this->link->autocommit(false);
		return $this->doScalar('select @@autocommit') == 0;
	}

	public function commitTransaction()
	{
		$commit = $this->link->commit();
		$this->link->autocommit(true);
		return $commit;
	}

	public function rollbackTransaction()
	{
		$rollback = $this->link->rollback();
		$this->link->autocommit(true);
		return $rollback;
	}

	public function inTransaction()
	{
		throw new Exception('Not supported');
	}

	public function driver()
	{
		return $this->link->get_client_info();
	}

	public function close()
	{
		$this->link->close();
		$this->link = null;
	}
	
	protected function setParameters(mysqli_stmt $statement, array $params = array())
	{
		if(!is_array($params) || count($params) == 0)
			return;
		$types = '';
		for($i = 0; $i < count($params); $i++, $types .= 's');
		$func_params[] = $types;
		foreach($params as $p) $func_params[] = $p;
		call_user_func_array(array($statement, 'bind_param'), $this->referenceParams($func_params));
	}

	private function referenceParams($params)
	{
		if(strpos(phpversion(), '5.3') !== false) {
			$references = array();
			foreach($params as $k => $param)
				$references[$k] = &$params[$k];
			return $references;
		}
		return $params;
	}
}
