<?php

class Url
{
	public static function google($q = null, $search = false)
	{
		$url = 'http://www.google.com';
		if($q !== null) {
			if($search)
				$url .= '/search';
			$url .= '?q=' . self::encode($q);
		}

		return $url;
	}

	public static function lmgtfy($q)
	{
		return 'http://lmgtfy.com?q=' . self::encode($q);
	}

	public static function encode($string)
	{
		return urlencode($string);
	}

	public static function decode($string)
	{
		return urldecode($string);
	}
}
