<?php
namespace smvc\helpers;
use smvc\base\RequestRegistry;

abstract class Redirect
{
	public static function to($url, array $vars = array())
	{
		$tmp = trim($url);
		if(strlen($tmp) == 0)
			return;
		header(sprintf('Location: %s%s', $url, self::querystring($vars)));
		die();
	}

	public static function refresh($time = 0, array $vars = array())
	{
		header("refresh: $time");
		die();
	}

	public static function here(array $vars = array())
	{
		self::refresh();
	}

	public static function command($command, array $vars = array())
	{
		$vars['cmd'] = $command;
		self::to('index.php', $vars);
	}

	public static function home(array $vars = array())
	{
		self::to('/');
	}
	
	private static function querystring(array $vars)
	{
		$querystring = array();
		foreach($vars as $key => $value)
			$querystring[] = sprintf('%s=%s', $key, $value);
		return sprintf('?%s', implode('&', $querystring));
	}
}
