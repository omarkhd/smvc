<?php

/*

__autoload uses a package approach for loading classes, if the root package
of a class' name is the application's name, it will exclude it from
the resulting include path, assuming that name as the DOCUMENT_ROOT name

*/

function __autoload($classname)
{
	$path = explode('\\', $classname);
	$strpath = $_SERVER["DOCUMENT_ROOT"];
	for($i = 0; $i < count($path); $strpath .= DIRECTORY_SEPARATOR . $path[$i++]);
	
	include_once $strpath . ".php";
	//echo $strpath;
}
