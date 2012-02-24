<?php

namespace system\model;
use PDO;
use PDOStatement;

class PDOCoreQueryStrategy implements IDriverCoreQueryStrategy
{
	private $pdo;
	
	public function __construct(array $db_info)
	{
		$host = $db_info["host"];
		$user = $db_info["user"];
		$pass = $db_info["password"];
		$name = $db_info["name"];
		$set_names = $db_info["set_names"];

		$dsn = "mysql:dbname=$name;host=$host";
		$this->pdo = new PDO($dsn, $user, $pass);
		$this->doNonQuery("set names '$set_names'");
	}
	
	public function doQuery($sql, array $params = null)
	{
		$statement = $this->pdo->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		$set = $statement->fetchAll(PDO::FETCH_ASSOC);
		if(count($set) > 0)
			return $set;
		return null;
	}

	public function doNonQuery($sql, array $params = null)
	{
		$statement = $this->pdo->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		return $statement->rowCount();
	}

	public function doScalar($sql, array $params = null)
	{
		$statement = $this->pdo->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		$record = $statement->fetchAll(PDO::FETCH_NUM);
		if(count($record) > 0)
			return $record[0][0];
		return null;
	}

	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	public function begin()
	{
		return $this->pdo->beginTransaction();
	}

	public function commit()
	{
		return $this->pdo->commit();
	}

	public function rollback()
	{
		return $this->pdo->rollBack();
	}

	public function driver()
	{
		return 'pdo: ' . $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	public function close()
	{
		$this->pdo = null;
	}
	
	private function setParameters(PDOStatement $statement, array $params =  null)
	{
		if(!is_array($params))
			return;

		for($i = 0; $i < count($params); $i++)
			$statement->bindParam($i + 1, $params[$i]);
	}
}
