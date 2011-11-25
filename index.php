<?php

function __autoload($classname)
{
	$path = explode('\\', $classname);
	$strpath = implode(DIRECTORY_SEPARATOR, $path);
	include_once $strpath . ".php";
}

\system\controller\Controller::run();
