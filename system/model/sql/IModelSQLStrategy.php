<?php
namespace smvc\model\sql;

interface IModelSQLStrategy
{
	public function escapeIdentifier($identifier);
	public function tableFQN($table_name);
	public function getAll($table);
	public function getBy($table, array $criteria);
	public function insert($table, array $values);
	public function truncate($table);
	public function deleteAll($table);
	public function deleteBy($table, array $criteria);
	public function updateAll($table, array $values);
	public function updateBy($table, array $criteria, array $values);
	public function existsBy($table, array $criteria);
	public function lastInsertId();
	public function now();
	public function today();
	public function currentTime();
	public function count($table, $field = null, array $criteria = array());
	public function max($table, $field, array $criteria = array());
	public function min($table, $field, array $criteria = array());
	public function sum($table, $field, array $criteria = array());
	public function avg($table, $field, array $criteria = array());
	public function distinct($table, array $fields, array $criteria = array());
}