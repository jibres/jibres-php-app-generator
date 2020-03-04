<?php

require_once "define.php";

class jibresAppGenerator
{

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
		curl_setopt($ch, CURLOPT_POST, true);
		// turn off some setting
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// timeout setting
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// error on result
		if ($result === false)
		{
			self::boboom(curl_error($ch). ':'. curl_errno($ch), true);
		}
		// empty result
		if (empty($result) || is_null($result) || !$result)
		{
			self::boboom('Empty server response', true);
		}
		curl_close($ch);

		// show result with jsonBoom
		self::jsonBoom($result, true);
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