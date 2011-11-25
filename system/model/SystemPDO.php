<?php

namespace system\model;

class SystemPDO
{
	//private static $Instance = null;
	private function __construct() {}
	private static $connections = array();

	private static function instancePDO($connection)
	{
		$registry = \system\base\RequestRegistry::getInstance();
		$databases = $registry->get("databases");

		if(!isset($databases[$connection]))
			return null;
		
		$db_info = $databases[$connection];
		$driver = $db_info["driver"];
		$host = $db_info["host"];
		$user = $db_info["user"];
		$pass = $db_info["password"];
		$name = $db_info["name"];
		$set_names = $db_info["set_names"];

		$dsn = "$driver:dbname=$name;host=$host";
		$pdo = new \PDO($dsn, $user, $pass);
		$pdo->exec("set names '$set_names'");
		return $pdo;
	}

	public static function getInstance($conn)
	{
		if(!isset(self::$connections[$conn]) || self::$connections[$conn] == null)
			self::$connections[$conn] = self::instancePDO($conn);

		return self::$connections[$conn];
	}

	public static function closeConnections()
	{
		self::$connections = null;
	}
}
