<?php
namespace smvc\model;
use smvc\base\RequestRegistry;
use smvc\model\driver;
use smvc\model\sql;
use Exception;

abstract class DatabaseFactory
{
	private static $strategies = array();

	private static $engineDrivers = array(
		'mysql' => array('pdo', 'mysqli'),
		'sqlite' => array('pdo'),
		'sqlite3' => array('pdo')
	);

	private static function assertSupportedEngine($engine, $connection_name)
	{
		if(!in_array($engine, array_keys(self::$engineDrivers)))
			throw new Exception(sprintf('Engine [%s] not supported in connection [%s]', $engine, $connection_name));
	}

	private static function getSelectedEngineDriver($connection_properties)
	{
		$engine_driver = reset(self::$engineDrivers[$connection_properties['engine']]);
		if(isset($connection_properties['driver']))
			$engine_driver = $connection_properties['driver'];
		if(!in_array($engine_driver, self::$engineDrivers[$connection_properties['engine']])) {
			throw new Exception(sprintf('Driver [%s] not supported for engine [%s]',
				$engine_driver, $connection_properties['engine']));
		}
		return $engine_driver;
	}

	private static function instanceDriverStrategy($connection)
	{
		$connection_properties = self::getProperties($connection);
		self::assertSupportedEngine($connection_properties['engine'], $connection);
		$selected_driver = self::getSelectedEngineDriver($connection_properties);
		if($selected_driver == 'pdo') {
			if($connection_properties['engine'] == 'mysql')
				return new driver\pdo\PDOMySQLDriverStrategy($connection_properties, self::getSQLStrategy($connection));
			else if(in_array($connection_properties['engine'], array('sqlite', 'sqlite3')))
				return new driver\pdo\PDOSQLiteDriverStrategy($connection_properties, self::getSQLStrategy($connection));
		}
		else if($selected_driver == 'mysqli') {
			return new driver\MySQLiDriverStrategy($connection_properties, self::getSQLStrategy($connection));
		}
		throw new Exception('Database driver strategy instance error');
	}

	private static function instanceSQLStrategy($connection)
	{
		$connection_properties = self::getProperties($connection);
		unset($connection_properties['password']);
		self::assertSupportedEngine($connection_properties['engine'], $connection);
		if($connection_properties['engine'] == 'mysql')
			return new sql\MySQLStrategy($connection_properties);
		else if(in_array($connection_properties['engine'], array('sqlite', 'sqlite3')))
			return new sql\SQLiteStrategy($connection_properties);
		throw new Exception('SQL strategy instance error');
	}

	public static function getDriverStrategy($connection)
	{
		if(!isset(self::$strategies[$connection]))
			self::$strategies[$connection] = array();
		if(!isset(self::$strategies[$connection]['driver']))
			self::$strategies[$connection]['driver'] = self::instanceDriverStrategy($connection);
		return self::$strategies[$connection]['driver'];
	}

	public static function getSQLStrategy($connection)
	{
		if(!isset(self::$strategies[$connection]))
			self::$strategies[$connection] = array();
		if(!isset(self::$strategies[$connection]['sql']))
			self::$strategies[$connection]['sql'] = self::instanceSQLStrategy($connection);
		return self::$strategies[$connection]['sql'];
	}

	public static function closeConnections()
	{
		foreach(self::$strategies as $connection => $connection_strategies) {
			if(isset($connection_strategies['driver'])) {
				$connection_strategies['driver']->close();
				unset(self::$strategies[$connection]['driver']);
			}
		}
	}

	private static function getProperties($connection)
	{
		$registry = RequestRegistry::getInstance();
		$settings = $registry->get('__settings__');
		if(!isset($settings['DATABASES'][$connection]))
			throw new Exception(sprintf('No database configuration for connection [%s]', $connection));
		return $settings['DATABASES'][$connection];
	}

	private static function getProperty($connection, $property)
	{
		$database_info = self::getProperties($connection);
		if(isset($database_info[$property]))
			return $database_info[$property];
		return null;
	}
}
