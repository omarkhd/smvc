<?php
/*
 * to use this settings, copy and paste this file under the name 'settings.php'
 * DON'T delete or just rename this file, because it's part of the source revision
 */

/*
 * APPLICATION_DIR
 * it's used to determine the application directory to find its respective settings and
 * to configure the autoloader when classes use the 'application' namespace, if no value
 * is provided it will use the default 'application' directory
 */
$APPLICATION_DIR = null;

/*
 * LOADERS
 * it's used to register more class loaders with spl_autoload_register after adding the ones
 * needed by the core of smvc, it should be an array of callable functions
 */
$LOADERS = array();

/*
 * LOGGING
 * it's used to toggle the smvc logging capability
 *
 * LOGGING_DATABASE
 * if the logging capability is enabled, this will be the database where all logging will be sent
 */
$LOGGING = false;
$LOGGING_DATABASE = array(
	'engine' => '',       # between mysql, postgres, sqlite
	'host' => '',         # the ip or domain of the database engine to connect
	'name' => '',         # the name of the db to connect, file path for sqlite
	'user' => '',         # username for authentication
	'password' => '',     # password for authentication
	'port' => ''          # overrides default port for engine, none for sqlite
);
