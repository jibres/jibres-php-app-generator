<?php

require_once "lib/fetcher.php";
require_once "lib/code.php";
require_once "lib/replacer.php";
require_once "lib/exec.php";
require_once "lib/cmd.php";

class jibresAppGenerator
{
	private static $STORE         = null;
	private static $STORE_CODE    = null;
	private static $API_DATA      = null;
	private static $VERSION_MAJOR = 1;
	public static  $VERSION_BUILD = 0;


	public static function run()
	{
		define('THIS', realpath(__DIR__));
		if(file_exists("lib/define.php"))
		{
			require_once "lib/define.php";
		}
		if(!defined('APP_FOLDER'))
		{
			define('APP_FOLDER', realpath(__DIR__ . '/../Jibres-app-android-v1'));
		}

		@set_time_limit(0);
		@ini_set('display_errors', 1);
		@ini_set('display_startup_errors', 1);
		@error_reporting(E_ALL);
		@date_default_timezone_set('Asia/Tehran');


		// start process
		jibresAppCode::process('start');

		// 1. check busy mode
		jibresAppCode::busy(true);

		// 2. get data
		jibresAppFetcher::run();
		jibresAppCode::process('fetch');

		// 3. run gradle
		jibresAppCmd::StopDaemons();
		jibresAppCmd::cleanApp();
		jibresAppCode::process('clean');
		jibresAppCmd::releaseApp();
		jibresAppCode::process('build');

		// 4. copy apk
		// $myVersion = 'jibres-'.self::store(). '-v'. self::version(). '.apk';
		$myVersion = 'jibres';
		if(self::store())
		{
			$myVersion .= '-'. self::store();
		}
		if(self::version())
		{
			$myVersion .= '-v'. self::version();
		}
		$myVersion .= '.apk';

		// remove $ from fileName
		$myVersion = str_replace('$', '', $myVersion);
		$path = 'v'. self::version(true). '/'. date("Ymd"). '/'. $myVersion;

		// $myTarget  = __DIR__. '/public_html/'. $myVersion;
		$myTarget  = __DIR__. '/public_html/'. $path;
		jibresAppReplacer::fill('/app/build/outputs/apk/release/app-release.apk', $myTarget, 'apk');

		// 5. call finish
		jibresAppFetcher::done(self::store(), $myVersion, $path);

		// 6. free busy mode
		jibresAppCode::busy(false);

		// 7. show finish message
		jibresAppCode::msg('Finish Successfull', true);
	}


	public static function version($_major = null)
	{
		$myVersion = self::$VERSION_MAJOR;
		if($_major)
		{
			return $myVersion;
		}

		$myVersion .= '.';
		// $myVersion .= date("Ymd");
		// $myVersion .= '.';
		$myVersion .= self::$VERSION_BUILD;

		return $myVersion;
	}


	public static function store($_store = false)
	{
		if($_store !== false)
		{
			self::$STORE = $_store;
			self::$STORE_CODE = str_replace('$', '', $_store);
			// replace store
			jibresAppReplacer::store(self::$STORE_CODE);
		}

		if(self::$STORE === null)
		{
			return 'jibres';
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