<?php

namespace system\model;
use Exception;
use system\base\RequestRegistry;

class DatabaseFactory
{
	private function __construct() {}
	private static $strategies = array();

	private static function instanceStrategy($connection)
	{
		$db_info = self::getProperties($connection);
		if($db_info == null)
			throw new Exception('There is no connection configuration of "' . $connection . '"');
		if(!isset($db_info['driver']))
			throw new Exception('No driver is specified for connection "' . $connection . '"');

		$strategy = null;
		$driver = $db_info['driver'];
		switch($driver) {
			case 'mysql':
				$strategy = new PDOCoreQueryStrategy($db_info);
				break;
			case 'mysqli':
				$strategy = new MySQLiCoreQueryStrategy($db_info);
				break;
			default:
				throw new Exception("Driver '$driver' not found for connection '$connection'");
		}

		return $strategy;
	}

	public static function getStrategy($conn)
	{
		if(!isset(self::$strategies[$conn]) || self::$strategies[$conn] == null)
			self::$strategies[$conn] = self::instanceStrategy($conn);

		return self::$strategies[$conn];
	}

	public static function closeConnections()
	{
		foreach(self::$strategies as $strategy)
			$strategy->close();

		self::$strategies = null;
	}

	public static function getProperties($connection)
	{
		$registry = RequestRegistry::getInstance();
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
