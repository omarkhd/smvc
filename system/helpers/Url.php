<?php

class Url
{
	private static $_base = null;

	public static function base($more = null)
	{
		if(self::$_base == null)
		{
			$r = \system\base\RequestRegistry::getInstance();
			self::$_base = $r->Get("base_url");
		}

		if($more == null)
			return self::$_base;
		return self::$_base . '/' . $more;
	}
}
