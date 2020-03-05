<?php

class jibresAppReplacer
{
	public static function store($_store)
	{
		// fill in store id file
		self::fill('/app/src/main/assets/secret/store.txt', $_store);

		// create application id
		$myAppID = 'com.jibres.'. $_store;
		self::gradle('/app/gradle.properties', ['APPLICATION_ID' => $myAppID]);

		// fill app name
		$appName = '<?xml version="1.0" encoding="utf-8"?><resources><string name="app_name">';
		$appName .= $_store;
		$appName .= '</string></resources>';
		self::fill('/app/src/main/res/values/app_name.xml', $appName);
	}


	public static function logo($_logoURL)
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
		return $myEndpoint;
	}


	public static function fill($_addr, $_data, $_copy = null)
	{
		$myAddr = APP_FOLDER. $_addr;
		if($_copy)
		{
			$file_headers = @get_headers($file);
			if(file_exists($_data))
			{
				copy($_data, $myAddr);
			}
			elseif($file_headers[0] != 'HTTP/1.1 404 Not Found')
			{
				copy($_data, $myAddr);
			}
			else
			{
				jibresAppCode::msg('Copy source is not exist! '. $_data, true);
			}
		}
		else
		{
			file_put_contents($myAddr, $_data);
		}
	}


	private static function gradle($_file, $_replace)
	{
		$fileAddr = APP_FOLDER. $_file;

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