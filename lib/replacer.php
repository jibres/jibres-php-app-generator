<?php

class jibresAppReplacer
{
	public static function store($_store)
	{
		$key_mode = 'debug';
		// create application id
		if($_store)
		{
			// fill in store id file
			self::fill('/app/src/main/assets/secret/store.txt', $_store);
			$myAppID  = 'com.jibres.'. $_store;
			$key_mode = 'jibres-business';
		}
		else
		{
			self::fill('/app/src/main/assets/secret/store.txt', 'jibres');
			$myAppID  = 'com.jibres';
			$key_mode = 'jibres';
		}

		$opt =
		[
			'APPLICATION_ID' => $myAppID,
			'KEY_ALIAS' => $key_mode,
		];
		self::gradle('/app/gradle.properties', $opt);
	}


	public static function appName($_name)
	{
		// fill app name
		$appName = '<?xml version="1.0" encoding="utf-8"?><resources><string name="app_name">';
		$appName .= $_name;
		$appName .= '</string></resources>';
		self::fill('/app/src/main/res/values/app_name.xml', $appName);
		self::fill('/app/src/main/res/values-fa-rIR/app_name.xml', $appName);
	}


	public static function logo($_logoURL)
	{
		self::fill('/app/src/main/res/drawable/logo.png', $_logoURL, true);
	}


	public static function endpoint($_storeMode, $_store = null)
	{
		$myEndpoint = 'https://core.jibres.ir/r10';
		if($_storeMode)
		{
			if($_store)
			{
				$myEndpoint = 'https://api.jibres.ir/'. $_store. '/v2';
			}
			else
			{
				jibresAppProcess::set('storeNotExist');
				jibresAppCode::msg('Store is not exist for endpoint', true);
			}
		}

		self::fill('/app/src/main/assets/secret/endpoint.txt', $myEndpoint);
		return $myEndpoint;
	}


	public static function fill($_addr, $_sourceData, $_copy = null)
	{
		$myAddr = APP_FOLDER. $_addr;
		if($_copy)
		{
			if($_copy === 'apk')
			{
				$myDir = dirname($_sourceData);
				if (!is_dir($myDir))
				{
					// dir doesn't exist, make it
					mkdir($myDir, 0775, true);
				}
				if(file_exists($myAddr))
				{
					copy($myAddr, $_sourceData);
				}
				else
				{
					jibresAppProcess::set('apkNotExist');
					jibresAppCode::msg('APK is not exist! '. $myAddr. ' - '. $_sourceData, true);
				}
			}
			else
			{
				$file_headers = @get_headers($_sourceData);

				if(isset($file_headers[0]))
				{
					if($file_headers[0] == 'HTTP/1.1 404 Not Found')
					{
						jibresAppProcess::set('copySourceNotExist');
						jibresAppCode::msg('Copy source is not exist URL! '. $_sourceData, true);
					}
					else
					{
						copy($_sourceData, $myAddr);
					}
				}
				else
				{
					jibresAppProcess::set('getHeaderFalse');
					jibresAppCode::msg('get_headers return false! '. $_sourceData, true);
				}
			}
		}
		else
		{
			$myDir = dirname($myAddr);
			if (!is_dir($myDir))
			{
				// dir doesn't exist, make it
				mkdir($myDir, 0775, true);
			}
			file_put_contents($myAddr, $_sourceData);
		}
	}


	private static function gradle($_file, $_replace)
	{
		$fileAddr = APP_FOLDER. $_file;

		if(!file_exists($fileAddr))
		{
			jibresAppProcess::set('iniNotFound');
			jibresAppCode::msg('ini not found! '. $_file, false);
			return null;
		}

		// Parse the file assuming it's structured as an INI file.
		// http://php.net/manual/en/function.parse-ini-file.php
		$data = parse_ini_file($fileAddr);
		$fileData = '';

		// gradle detail to set
		// APPLICATION_ID=com.jibres.android
		// STORE_FILE=xxx
		// STORE_PASSWORD=xxx
		// KEY_ALIAS=xxx
		// KEY_PASSWORD=xxx

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