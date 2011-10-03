<?php

namespace system\model;

class SystemPDO
{
	private static $Instance = null;

	private function __construct() {}

	private static function InstancePDO()
	{
		$registry = \system\base\RequestRegistry::GetInstance();
		$host = $registry->Get("db_host");
		$user = $registry->Get("db_user");
		$pass = $registry->Get("db_password");
		$name = $registry->Get("db_name");
		$set_names = $registry->Get("set_names");

		$dsn = "mysql:dbname=$name;host=$host";
		$pdo = new \PDO($dsn, $user, $pass);
		$pdo->exec("set names '$set_names'");
		return $pdo;
	}

	public static function GetInstance()
	{
		if(self::$Instance == null)
			self::$Instance = self::InstancePDO();

		return self::$Instance;
	}
}
