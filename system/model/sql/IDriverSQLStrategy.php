<?php
namespace smvc\model\sql;

interface IDriverSQLStrategy
{
	public function beginTransaction();
	public function commitTransaction();
	public function rollbackTransaction();
	public function inTransaction();
	public function setNames($charset);
}