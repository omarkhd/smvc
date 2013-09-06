<?php

namespace smvc\model\driver;
use smvc\model\sql\IDriverSQLStrategy;

interface IDriverStrategy
{
	public function __construct(array $connection_properties, IDriverSQLStrategy $sql_strategy);
	public function doQuery($sql, array $params = null);
	public function doNonQuery($sql, array $params = null);
	public function doScalar($sql, array $params = null);
	public function lastInsertId();
	public function beginTransaction();
	public function commitTransaction();
	public function rollbackTransaction();
	public function inTransaction();
	public function driver();
	public function close();
}
