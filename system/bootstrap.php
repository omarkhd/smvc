<?php
/* if used in a FrontController environment, this file should be included automatically, and the easiest way could
 * be done with the PHP's auto_prepend_file directive
 */

abstract class Bootstrap
{
	private static $SMVC_SYSTEM_DIR;
	private static $SMVC_APPLICATION_DIR;
	private static $LOADERS = array();

	public static function run()
	{
		self::$SMVC_SYSTEM_DIR = __DIR__;
		self::readSettings();
		self::registerLoaders();
	}

	private static function readSettings()
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
		$dir_info = new SplFileInfo(__DIR__);
		self::$SMVC_APPLICATION_DIR = $dir_info->getPath() . '/application';
	}

	private static function registerLoaders()
	{
		$smvc_system_dir = self::$SMVC_SYSTEM_DIR;
		$smvc_application_dir = self::$SMVC_APPLICATION_DIR;
		/* default loader */
		spl_autoload_register(function($classname) use ($smvc_system_dir, $smvc_application_dir) {
			$namespace_path = explode('\\', $classname);
			$root_namespace = array_shift($namespace_path);
			if(in_array($root_namespace, array('smvc', 'application'))) {
				$strpath = implode(DIRECTORY_SEPARATOR, $namespace_path);
				require_once sprintf('%s/%s.php',
					$root_namespace == 'smvc' ? $smvc_system_dir : $smvc_application_dir, $strpath);
			}
		});
		/* extra loaders added in the settings file */
		foreach(self::$LOADERS as $loader)
			spl_autoload_register($loader);
	}

	private static function loadCore()
	{
	}
}

Bootstrap::run();
