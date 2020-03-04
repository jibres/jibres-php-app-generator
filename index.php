<?php

require_once "lib/fetcher.php";
require_once "lib/code.php";
require_once "lib/replacer.php";

class jibresAppGenerator
{
	private static $STORE = null;
	private static $API_DATA = null;

	public static function run()
	{
		if(file_exists("lib/define.php"))
		{
			require_once "lib/define.php";
		}
		else
		{
			self::msg('define variables!', false);
		}

		// get data
		jibresAppFetcher::fetchAPI();
		// fill data
		jibresAppReplacer::replaceVar();
		// run gradle

		// copy apk

		// call finish
	}


	public static function store($_store = null)
	{
		if($_store)
		{
			self::$STORE = $_store;
		}
		return self::$STORE;
	}


	public static function apiData($_data = null)
	{
		if($_data)
		{
			self::$API_DATA = $_data;
		}
		return self::$API_DATA;
	}
}

// run for the first time
\jibresAppGenerator::run();
?>