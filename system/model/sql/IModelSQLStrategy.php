<?php
namespace smvc\model\sql;

interface IModelSQLStrategy
{
	public function escapeIdentifier($identifier);
	public function tableFQN($table_name);
}