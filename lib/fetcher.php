<?php

class jibresAppFetcher
{
	public static function fetchAPI()
	{
		// get store id
		$myStore = self::getQueue(CORE_QUEUE);
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			jibresAppGenerator::STORE($myStore['result']['store']);
		}
		// get store api android
		$a = self::getQueue(self::create_api_link(API_ANDROID));
		jibresAppGenerator::apiData($a);
		// get store api android intro
		// get store api android splash
	}


	private static function create_api_link($_url)
	{
		return str_replace(':store', jibresAppGenerator::STORE(), $_url);
	}


	private static function getQueue($_url)
	{
		$ch = curl_init();

		if ($ch === false)
		{
			jibresAppCode::boboom('Curl failed to initialize');
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
			jibresAppCode::boboom(curl_error($ch). ':'. curl_errno($ch), true);
		}
		// empty result
		if (empty($response) || is_null($response) || !$response)
		{
			jibresAppCode::boboom('Empty server response', true);
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

		jibresAppCode::jsonBoom($response);
	}
}
?>