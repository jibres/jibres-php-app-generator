<?php

class jibresAppReplacer
{
	private static $APP_FOLDER = __DIR__ . '/../../Jibres-AndroidApp';

	public function replaceVar()
	{
		// replace app/gradle.properties
		$myAppID = 'com.jibres.'. jibresAppGenerator::store();
		self::gradle('/app/gradle.properties', ['APPLICATION_ID' => $myAppID]);

	}


	public static function store($_store)
	{
		self::fill('/app/src/main/assets/secret/store.txt', $_store);
	}


	private static function logo($_logoURL)
	{
		self::fill('/app/src/main/res/drawable/logo.png', $_logoURL, true);
	}


	public static function endpoint($_storeMode, $_store = null)
	{
		$myEndpoint = 'https://core.jibres.com/r10';
		if($_storeMode)
		{
			if($_store)
			{
				$myEndpoint = 'https://api.jibres.com/'. $_store. '/v2';
			}
			else
			{
				jibresAppCode::msg('Store is not exist for endpoint', true);
			}
		}

		self::fill('/app/src/main/assets/secret/endpoint.txt', $myEndpoint);
	}

	private static function fill($_addr, $_data, $_copy = null)
	{
		// create path
		$myAddr = realpath(__DIR__ . '/../../Jibres-AndroidApp');
		$myAddr .= $_addr;

		if($_copy)
		{
			copy($_data, $myAddr);
		}
		else
		{
			file_put_contents($myAddr, $_data);
		}
	}

	private static function gradle($_file, $_replace)
	{
		$fileAddr = realpath(self::$APP_FOLDER. $_file);

		if(!file_exists($fileAddr))
		{
			jibresAppCode::msg('ini not found! '. $_file, false);
			return null;
		}

		// Parse the file assuming it's structured as an INI file.
		// http://php.net/manual/en/function.parse-ini-file.php
		$data = parse_ini_file($fileAddr);
		$fileData = '';

		foreach($data as $key => $value)
		{
			if(isset($_replace[$key]) && $_replace[$key] !== $value)
			{
				$fileData .= $key. '='. $_replace[$key]. PHP_EOL;
			}
			else
			{
				$fileData .= $key. '='. $value. PHP_EOL;
			}
		}

		file_put_contents($fileAddr, $fileData);

		return true;
	}
}
?>