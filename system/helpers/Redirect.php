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

	public static function here($keep_method = false, array $vars = array())
	{
		$keep_method ? self::refresh() : self::to($_SERVER['PHP_SELF']);
	}

	public static function command($command, array $vars = array())
	{
		$vars['cmd'] = $command;
		self::to('index.php', $vars);
	}

	public static function home($relative_path = null, array $vars = array())
	{
		self::to(sprintf('/%s', $relative_path), $vars);
	}
	
	private static function querystring(array $vars = array())
	{
		$querystring = array();
		foreach($vars as $key => $value)
			$querystring[] = sprintf('%s=%s', $key, $value);
		return $querystring ? sprintf('?%s', implode('&', $querystring)) : '';
	}
}
