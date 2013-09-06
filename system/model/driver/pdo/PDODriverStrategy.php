<?php
namespace smvc\model\driver\pdo;
use smvc\model\driver\IDriverStrategy;
use smvc\model\sql\IDriverSQLStrategy;
use smvc\model\ResultSet;
use PDO, PDOStatement, Exception;

abstract class PDODriverStrategy implements IDriverStrategy
{
	protected $pdo = null;
	protected $sqlStrategy = null;
	
	public function __construct(array $connection_properties, IDriverSQLStrategy $sql_strategy)
	{
		$this->sqlStrategy = $sql_strategy;
		$this->pdo = $this->instancePDO($connection_properties);
		if($this->pdo == null || !($this->pdo instanceof PDO))
			throw new Exception('PDO link instance could not be instantiated');
		echo $this->sqlStrategy->setNames('utf8');
	}

	abstract protected function instancePDO($connection_properties);

	public function doQuery($sql, array $params = null)
	{
		$statement = $this->pdo->prepare($sql);
		$this->setParameters($statement, $params);
		$statement->execute();
		return new ResultSet($statement->fetchAll(PDO::FETCH_ASSOC));
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

	public function beginTransaction()
	{
		return $this->pdo->beginTransaction();
	}

	public function commitTransaction()
	{
		return $this->pdo->commit();
	}

	public function rollbackTransaction()
	{
		return $this->pdo->rollBack();
	}

	public function inTransaction()
	{
		return $this->pdo->inTransaction();
	}

	public function driver()
	{
		return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
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