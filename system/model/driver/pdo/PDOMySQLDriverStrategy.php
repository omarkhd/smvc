<?php
namespace smvc\model\driver\pdo;
use PDO;

class PDOMySQLDriverStrategy extends PDODriverStrategy
{
	protected function instancePDO($connection_properties)
	{
		$dsn = sprintf('mysql:dbname=%s;host=%s', $connection_properties['name'], $connection_properties['host']);
		return new PDO($dsn, $connection_properties['user'], $connection_properties['password']);
	}
}