<?php

/*
 *	__autoload uses a namespace/package approach for loading classes
 */

function __autoload($classname)
{
	$path = explode('\\', $classname);
	$strpath = $_SERVER["DOCUMENT_ROOT"];
	for($i = 0; $i < count($path); $strpath .= DIRECTORY_SEPARATOR . $path[$i++]);
	include_once $strpath . ".php";
}
