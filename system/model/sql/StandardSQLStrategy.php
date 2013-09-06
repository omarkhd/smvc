<?php
namespace smvc\model\sql;

abstract class StandardSQLStrategy implements IModelSQLStrategy, IDriverSQLStrategy
{
	/*
	 * Model's basic SQL generation strategies
	 */

	 public function escapeIdentifier($identifier)
	 {
	 	return sprintf('"%s"', $identifier);
	 }

	/*
	 * Driver's basic SQL generation strategies
	 */

	public function beginTransaction()
	{
		return 'start transaction';
	}

	public function commitTransaction()
	{
		return 'commit';
	}

	public function rollbackTransaction()
	{
		return 'rollback';
	}
}