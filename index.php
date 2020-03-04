<?php


class jibresAppGenerator
{
	private static $STORE = null;

	function run()
	{
		if(file_exists("define.php"))
		{
			require_once "define.php";
		}
		else
		{
			self::jsonBoom(['ok' => false, 'result' => 'define variables!']);
		}

		$myStore = self::getQueue(CORE_QUEUE);
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			self::$STORE = $myStore['result']['store'];
		}
		var_dump(self::$STORE);

		$android_api = self::getQueue(self::create_api_link(API_ANDROID));
		var_dump($android_api);

	}



	function create_api_link($_url)
	{
		return str_replace(':store', self::$STORE, $_url);
	}

	function getQueue($_url)
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