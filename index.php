<?php

require_once "lib/fetcher.php";
require_once "lib/code.php";
require_once "lib/replacer.php";

class jibresAppGenerator
{
	private static $STORE         = null;
	private static $API_DATA      = null;
	private static $VERSION_MAJOR = 1;
	public static  $VERSION_BUILD = 0;



	public static function run()
	{
		// if(file_exists("lib/define.php"))
		// {
		// 	require_once "lib/define.php";
		// }
		// else
		// {
		// 	jibresAppCode::msg('define variables!', false);
		// }
		define('APP_FOLDER', realpath(__DIR__ . '/../Jibres-app-android-v1'));
		define('THIS', realpath(__DIR__));

		// 1. check busy mode
		jibresAppCode::busy(true);

		// 2. get data
		jibresAppFetcher::run();

		// 3. run gradle
		self::buildApp();

		// 4. copy apk
		$myVersion = 'v'. self::version(true);
		$myVersion .= '/jibres-'.self::store();
		$myVersion .= '-v'. self::version(). '.apk';
		$myVersion = str_replace('$', '', $myVersion);

		// $myTarget  = __DIR__. '/public_html/'. $myVersion;
		$myTarget  = __DIR__. '/public_html/'. date("Ymd"). '/'. $myVersion;
		jibresAppReplacer::fill('/app/build/outputs/apk/release/app-release.apk', $myTarget, 'apk');

		// 5. call finish
		jibresAppFetcher::done(self::store(), $myVersion);

		// 6. free busy mode
		jibresAppCode::busy(false);

		// 7. show finish message
		jibresAppCode::msg('Finish Successfull', true);
	}


	private static function version($_major = null)
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


	private static function buildApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew assembleRelease';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, 'build');
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