<?php

require_once "lib/fetcher.php";
require_once "lib/code.php";
require_once "lib/replacer.php";

class jibresAppGenerator
{
	private static $STORE = null;
	private static $API_DATA = null;
	private static $VERSION = 1;


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
		define('APP_FOLDER', realpath(__DIR__ . '/../Jibres-AndroidApp'));

		// get data
		jibresAppFetcher::run();
		// run gradle

		// copy apk

		$target = __DIR__. '/public_html/v'. self::$VERSION;
		$target .= '/jibres-'.self::store().'-v'. self::$VERSION. '.apk';
		var_dump($target);
		exit();
		jibresAppReplacer::fill('app/build/outputs/apk/release/app-release.apk', $target, true);
		// call finish

		jibresAppCode::msg('Finish Successfull', true);
	}


	public static function store($_store = false)
	{
		if($_store !== false)
		{
			self::$STORE = $_store;
			// replace store
			jibresAppReplacer::store($_store);
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