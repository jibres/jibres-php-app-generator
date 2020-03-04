<?php


class jibresAppGenerator
{
	private static $STORE = null;
	private static $API_DATA = null;

	public static function run()
	{
		if(file_exists("define.php"))
		{
			require_once "define.php";
		}
		else
		{
			self::msg('define variables!', false);
		}
		// get data
		self::get_api_data();
		// fill data
		self::replaceVariables();
		// run gradle

		// copy apk

		// call finish
	}


	private static get_api_data()
	{
		// get store id
		$myStore = self::getQueue(CORE_QUEUE);
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			self::$STORE = $myStore['result']['store'];
		}
		// get store api android
		self::$API_DATA = self::getQueue(self::create_api_link(API_ANDROID));
		// get store api android intro
		// get store api android splash
	}


	private function replaceVariables()
	{
		// replace app/gradle.properties
		$myAppID = 'com.jibres.'. self::$STORE;
		self::replace_ini(APP_FOLDER. '/app/gradle.properties', ['APPLICATION_ID' => $myAppID]);

	}


	private static function replace_ini($_file, $_replace)
	{
		$fileAddr = realpath($_file);

		if(!file_exists($fileAddr))
		{
			self::msg('ini not found! '. $_file, false);
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


	private static function create_api_link($_url)
	{
		return str_replace(':store', self::$STORE, $_url);
	}


	private static function getQueue($_url)
	{
		$ch = curl_init();

		if ($ch === false)
		{
			self::boboom('Curl failed to initialize');
		}

		curl_setopt($ch, CURLOPT_URL, $_url);
		// turn on some setting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, false);
		// turn off some setting
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// timeout setting
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$response = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$myResponse = null;
		$myResult = null;
		// error on result
		if ($response === false)
		{
			self::boboom(curl_error($ch). ':'. curl_errno($ch), true);
		}
		// empty result
		if (empty($response) || is_null($response) || !$response)
		{
			self::boboom('Empty server response', true);
		}
		curl_close($ch);
		if(substr($response, 0, 1) === "{")
		{
			$myResponse = json_decode($response, JSON_UNESCAPED_UNICODE);
		}
		return $myResponse;

		if(isset($myResponse['ok']))
		{
			return $myResponse;
		}

		self::jsonBoom($response);
	}


	public static function boboom($_string = null)
	{
		// change header
		exit($_string);
	}

	public static function msg($_txt = null, $_status)
	{
		self::jsonBoom(['ok'=> $_status, 'result'=> $_txt]);
	}

	public static function jsonBoom($_result = null)
	{
		if(is_array($_result))
		{
			$_result = json_encode($_result, JSON_UNESCAPED_UNICODE);
		}

		if(substr($_result, 0, 1) === "{")
		{
			@header("Content-Type: application/json; charset=utf-8");
		}
		echo $_result;
		self::boboom();
	}
}

// run for the first time
\jibresAppGenerator::run();
?>