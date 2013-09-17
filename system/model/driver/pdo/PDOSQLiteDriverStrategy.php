<?php
namespace smvc\model\driver\pdo;
use PDO;

class PDOSQLiteDriverStrategy extends PDODriverStrategy
{
	protected function instancePDO($db_info)
	{
		$pdo_engine_string = $db_info['engine'] == 'sqlite3' ? 'sqlite' : 'sqlite2';
		return new PDO(sprintf('%s:%s', $pdo_engine_string, $db_info['name']));
	}
}