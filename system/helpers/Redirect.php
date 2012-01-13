<?php

abstract class Redirect
{
	public static function to($url, $exit = true)
	{
		$tmp = trim($url);
		if(strlen($tmp) == 0)
			return;
		
		header("Location: $url");
		if($exit) die();
	}

	public static function refresh($time = 0)
	{
		header("refresh: $time");
		die();
	}

	public static function here()
	{
		self::refresh();
	}

	public static function command($command)
	{
		self::to("index.php?cmd=$command");
	}

	public static function home()
	{
		self::to('index.php');
	}
}
