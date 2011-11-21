<?php

namespace system\model;

class SystemPDO
{
	//private static $Instance = null;
	private function __construct() {}
	private static $Connections = array();

	private static function InstancePDO($connection)
	{
		$registry = \system\base\RequestRegistry::GetInstance();
		$databases = $registry->Get("databases");

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

	public static function GetInstance($conn)
	{
		if(!isset(self::$Connections[$conn]) || self::$Connections[$conn] == null)
			self::$Connections[$conn] = self::InstancePDO($conn);

		return self::$Connections[$conn];
	}

	public static function CloseConnections()
	{
		self::$Connections = null;
	}
}
