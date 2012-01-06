<?php

namespace system\model;

class DatabaseFactory
{
	//private static $Instance = null;
	private function __construct() {}
	private static $connections = array();

	private static function instancePDO($connection)
	{
		$db_info = self::getProperties($connection);
		$pdo = null;

		try {
			$driver = $db_info["driver"];
			$host = $db_info["host"];
			$user = $db_info["user"];
			$pass = $db_info["password"];
			$name = $db_info["name"];
			$set_names = $db_info["set_names"];

			$dsn = "$driver:dbname=$name;host=$host";
			$pdo = new \PDO($dsn, $user, $pass);
			$pdo->exec("set names '$set_names'");
		} catch(\Exception $e) {}
			
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

	public static function getProperties($connection)
	{
		$registry = \system\base\RequestRegistry::getInstance();
		$databases = $registry->get("databases");

		if(!isset($databases[$connection]))
			return null;
		return $databases[$connection];
	}

	public static function getProperty($connection, $property)
	{
		$dbinfo = self::getProperties($connection);
		if(isset($dbinfo[$property]))
			return $dbinfo[$property];
		return null;
	}
}
