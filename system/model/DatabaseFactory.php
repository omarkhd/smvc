<?php

namespace smvc\model;
use smvc\base\RequestRegistry;
use Exception;

class DatabaseFactory
{
	private function __construct() {}
	private static $strategies = array();

	private static function instanceStrategy($connection)
	{
		$database_properties = self::getProperties($connection);
		if(!$database_properties)
			throw new Exception('There is no connection configuration of "' . $connection . '"');
		if(!isset($database_properties['engine']))
			throw new Exception('No engine is specified for connection "' . $connection . '"');
		$strategy = null;
		$engine = $database_properties['engine'];
		switch($engine) {
			case 'mysql':
				$strategy = new PDOCoreQueryStrategy($database_properties);
				break;
			case 'mysqli':
				$strategy = new MySQLiCoreQueryStrategy($database_properties);
				break;
			default:
				throw new Exception("Engine '$engine' not found for connection '$connection'");
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
		$settings = $registry->get('__settings__');
		if(!isset($settings['DATABASES'][$connection]))
			return null;
		return $settings['DATABASES'][$connection];
	}

	public static function getProperty($connection, $property)
	{
		$dbinfo = self::getProperties($connection);
		if(isset($dbinfo[$property]))
			return $dbinfo[$property];
		return null;
	}
}
