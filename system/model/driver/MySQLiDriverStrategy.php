<?php

namespace smvc\model\driver;
use mysqli, mysqli_stmt, Exception;

class MySQLiDriverStrategy implements IDriverStrategy
{
	private $link;
	
	public function __construct(array $db_info)
	{
		$this->link = new mysqli($db_info['host'], $db_info['user'], $db_info['password'], $db_info['name']);
		if(mysqli_connect_error())
			throw new Exception(mysqli_connect_error());
	}
	
	public function doQuery($sql, array $params = null)
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
		return count($result_set) > 0 ? $result_set : null;
	}

	public function doNonQuery($sql, array $params = null)
	{
		$statement = $this->link->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		return $statement->affected_rows;
	}

	public function doScalar($sql, array $params = null)
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
			foreach($placeholder as $column => $value)
				return $value; //returning only the first value found in the result set
		return null; //or return null
	}

	public function lastInsertId()
	{
		return $this->doScalar('select last_insert_id()');
	}

	public function begin()
	{
		$this->link->autocommit(false);
		return $this->doScalar('select @@autocommit') == 0;
	}

	public function commit()
	{
		$commit = $this->link->commit();
		$this->link->autocommit(true);
		return $commit;
	}

	public function rollback()
	{
		$rollback = $this->link->rollback();
		$this->link->autocommit(true);
		return $rollback;
	}

	public function driver()
	{
		return 'mysqli: ' . $this->link->get_client_info();
	}

	public function close()
	{
		$this->link->close();
		$this->link = null;
	}
	
	protected function setParameters(mysqli_stmt $statement, array $params =  null)
	{
		if(!is_array($params))
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
