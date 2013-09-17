<?php

namespace smvc\model\driver;
use smvc\model\sql\IDriverSQLStrategy;

interface IDriverStrategy
{
	public function __construct(array $connection_properties, IDriverSQLStrategy $sql_strategy);
	public function doQuery($sql, array $params = array());
	public function doNonQuery($sql, array $params = array());
	public function doScalar($sql, array $params = array());
	public function lastInsertId();
	public function beginTransaction();
	public function commitTransaction();
	public function rollbackTransaction();
	public function inTransaction();
	public function driver();
	public function close();
}
