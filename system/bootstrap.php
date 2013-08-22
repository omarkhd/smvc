<?php
/* if used in a FrontController environment, this file should be included automatically, and the easiest way could
 * be done with the PHP's auto_prepend_file directive
 */

abstract class Bootstrap
{
	private static $SMVC_SYSTEM_DIR;
	private static $SMVC_APPLICATION_DIR;

	public static function run()
	{
		self::$SMVC_SYSTEM_DIR = __DIR__;
		$this_dir_info = new SplFileInfo(__DIR__);
		self::$SMVC_APPLICATION_DIR = $this_dir_info->getPath() . '/application';
		self::registerSmvcLoader();
	}

	private static function registerSmvcLoader()
	{
		$smvc_system_dir = self::$SMVC_SYSTEM_DIR;
		$smvc_application_dir = self::$SMVC_APPLICATION_DIR;
		spl_autoload_register(function($classname) use ($smvc_system_dir, $smvc_application_dir) {
			$namespace_path = explode('\\', $classname);
			$root_namespace = array_shift($namespace_path);
			if(in_array($root_namespace, array('smvc', 'application'))) {
				$strpath = implode(DIRECTORY_SEPARATOR, $namespace_path);
				require_once sprintf('%s/%s.php',
					$root_namespace == 'smvc' ? $smvc_system_dir : $smvc_application_dir, $strpath);
			}
		});
	}
}

Bootstrap::run();
