<?php

namespace smvc\model;

interface IDriverCoreQueryStrategy
{
	public function __construct(array $db_info);
	public function doQuery($sql, array $params = null);
	public function doNonQuery($sql, array $params = null);
	public function doScalar($sql, array $params = null);
	public function lastInsertId();
	public function begin();
	public function commit();
	public function rollback();
	public function driver();
	public function close();
}
