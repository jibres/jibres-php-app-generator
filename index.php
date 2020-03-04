<?php

require_once "define.php";

class jibresAppGenerator
{
	private static $STORE = null;

	function run()
	{
		self::getQueue();
	}


	function getQueue()
	{
		$ch = curl_init();

		if ($ch === false)
		{
			self::boboom('Curl failed to initialize');
		}
		// set some settings of curl
		$apiURL = API_URL;

		curl_setopt($ch, CURLOPT_URL, $apiURL);
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

		if(isset($myResponse['ok']))
		{
			if($myResponse['ok'] === true)
			{
				if(isset($myResponse['result']))
				{
					$myResult = $myResponse['result'];
				}
			}
		}
		if(isset($myResult['store']) && $myResult['store'])
		{
			self::$STORE = $myResult['store'];
			return $myResult['store'];
		}

		self::jsonBoom($response);
	}


	public static function boboom($_string = null)
	{
		// change header
		exit($_string);
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