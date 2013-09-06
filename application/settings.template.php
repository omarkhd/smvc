<?php
/*
 * to use this settings, copy and paste this file under the name 'settings.php'
 * DON'T delete or just rename this file, because it's part of the source revision
 */

$APPLICATION_NAME = null;
$APPLICATION_DESCRIPTION = null;

/*
 * APPLICATION_NAMESPACE
 * this namespace is used as the root namespace for the application, and will be used to autoload
 * classes (in a directory structured fashion) that belong to this namespace; if the namespce is not
 * specified or it's null, the namespace 'application' will be used
 */
$APPLICATION_NAMESPACE = null;

/*
 * DATABASES
 * connections used to access database engines, every connections should have a name, and you can have
 * several connections each one with different engine (mysql, sqlite, postgresql)
 */
$DATABASES = array(
	'default' => array(
		'engine' => '',
		'host' => '',
		'name' => '',
		'user' => '',
		'password' => '',
		'set_names' => ''
	)
);

/*
 * SESSION_NAME
 * this name is used with the php's session_name function when using the SessionRegister class, if its
 * value it's null, the function will not be called
 */
$SESSION_NAME = null;

/*
 * LOADERS
 * it's used to register more class loaders with spl_autoload_register after adding the ones
 * needed by the core of smvc, it should be an array of callable functions
 * all these extra loaders will be registered after the ones in system/settings
 */
$LOADERS = array();

/*
 * VIEWS_DIRS
 * these directories will be used to look for views, in the order they are added here, if no value is given
 * it will be used the 'views' subdirectory of the application directory
 */
$VIEW_DIRS = array();