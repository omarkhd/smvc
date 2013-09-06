<?php
namespace smvc;
/* if used in a FrontController environment, this file should be included automatically, and the easiest way could
 * be done with the PHP's auto_prepend_file directive
 */

abstract class Bootstrap
{
	/* system settings */
	private static $SMVC_SYSTEM_DIR;
	private static $SMVC_APPLICATION_DIR;
	private static $LOADERS = array();
	private static $APPLICATION_SETTINGS;

	public static function run()
	{
		self::$SMVC_SYSTEM_DIR = __DIR__;
		self::loadSettings();
		self::loadApplicationSettings();
		self::registerLoaders();
		self::registerApplicationSettings();
	}

	private static function loadSettings()
	{
		self::setDefaultSettings();
		$settings_file = __DIR__ . '/settings.php';
		if(!file_exists($settings_file))
			return;
		include $settings_file;
		/* application directory */
		if(isset($APPLICATION_DIR) && $APPLICATION_DIR)
			self::$SMVC_APPLICATION_DIR = $APPLICATION_DIR;
		/* extra loaders */
		if(isset($LOADERS) && is_array($LOADERS)) {
			foreach($LOADERS as $loader) {
				if(is_callable($loader))
					self::$LOADERS[] = $loader;
			}
		}
	}

	private static function setDefaultSettings()
	{
		$dir_info = new \SplFileInfo(__DIR__);
		self::$SMVC_APPLICATION_DIR = $dir_info->getPath() . '/application';
	}

	private static function loadApplicationSettings()
	{
		self::setDefaultApplicationSettings();
		$setings_file = self::$SMVC_APPLICATION_DIR . '/settings.php';
		if(!file_exists($setings_file))
			return;
		$settings = self::$APPLICATION_SETTINGS;
		include $setings_file;
		if(isset($APPLICATION_NAME) && $APPLICATION_NAME)
			$settings['APPLICATION_NAME'] = $APPLICATION_NAME;
		if(isset($APPLICATION_DESCRIPTION) && $APPLICATION_DESCRIPTION)
			$settings['APPLICATION_DESCRIPTION'] = $APPLICATION_DESCRIPTION;
		if(isset($APPLICATION_NAMESPACE) && $APPLICATION_NAMESPACE)
			$settings['APPLICATION_NAMESPACE'] = $APPLICATION_NAMESPACE;
		if(isset($DATABASES) && is_array($DATABASES))
			$settings['DATABASES'] = $DATABASES;
		if(isset($SESSION_NAME) && $SESSION_NAME)
			$settings['SESSION_NAME'] = $SESSION_NAME;
		if(isset($LOADERS) && is_array($LOADERS)) {
			foreach($LOADERS as $loader) {
				if(is_callable($loader))
					self::$LOADERS[] = $loader;
			}
		}
		if(isset($VIEW_DIRS) && is_array($VIEW_DIRS) && count($VIEW_DIRS) > 0)
			$settings['VIEW_DIRS'] = $VIEW_DIRS;
		self::$APPLICATION_SETTINGS = $settings;
	}

	private static function setDefaultApplicationSettings()
	{
		$settings = array();
		$settings['APPLICATION_DIR'] = self::$SMVC_APPLICATION_DIR;
		$settings['SYSTEM_DIR'] = self::$SMVC_SYSTEM_DIR;
		$settings['APPLICATION_NAME'] = null;
		$settings['APPLICATION_DESCRIPTION'] = null;
		$settings['APPLICATION_NAMESPACE'] = 'application';
		$settings['DATABASES'] = array();
		$settings['SESSION_NAME'] = null;
		$settings['VIEW_DIRS'] = array(self::$SMVC_APPLICATION_DIR . '/views');
		self::$APPLICATION_SETTINGS = $settings;
	}

	private static function registerLoaders()
	{
		$smvc_system_dir = self::$SMVC_SYSTEM_DIR;
		$smvc_application_dir = self::$SMVC_APPLICATION_DIR;
		$application_namespace = self::$APPLICATION_SETTINGS['APPLICATION_NAMESPACE'];
		/* default loader */
		spl_autoload_register(function($classname) use ($smvc_system_dir, $smvc_application_dir, $application_namespace) {
			$namespace_path = explode('\\', $classname);
			$root_namespace = array_shift($namespace_path);
			if(in_array($root_namespace, array('smvc', $application_namespace))) {
				$strpath = implode(DIRECTORY_SEPARATOR, $namespace_path);
				require_once sprintf('%s/%s.php',
					$root_namespace == 'smvc' ? $smvc_system_dir : $smvc_application_dir, $strpath);
			}
		});
		/* extra loaders added in the settings file */
		foreach(self::$LOADERS as $loader)
			spl_autoload_register($loader);
	}

	private static function registerApplicationSettings()
	{
		$registry = \smvc\base\RequestRegistry::getInstance();
		$registry->set('__settings__', self::$APPLICATION_SETTINGS);
	}
}

Bootstrap::run();
