<?php

class jibresAppFetcher
{
	private static $API_ANDROID = 'https://api.jibres.com/:store/v2/android';
	private static $CORE_ANDROID = 'https://core.jibres.com/r10/android';

	public static function fetchAPI()
	{
		// get store id
		$myStore = self::get_api_data('https://core.jibres.com/r10/queue/app', true);
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			jibresAppGenerator::STORE($myStore['result']['store']);
			jibresAppReplacer::endpoint(true, $myStore['result']['store']);
		}
		elseif(isset($myStore['result']['jibres']) && $myStore['result']['jibres'] === true)
		{
			jibresAppGenerator::STORE(null);
			jibresAppReplacer::endpoint(null);
		}
		else
		{
			jibresAppCode::msg('Queue is empty', true);
		}

		// get store api android
		$a = self::get_api_data(self::create_api_link(self::$API_ANDROID));
		jibresAppGenerator::apiData($a);
		// get store api android intro
		// get store api android splash
	}


	private static function create_api_link($_url)
	{
		return str_replace(':store', jibresAppGenerator::STORE(), $_url);
	}


	private static function get_api_data($_url, $_json = null)
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

		if($_json && substr($response, 0, 1) === "{")
		{
			return json_decode($response, JSON_UNESCAPED_UNICODE);
		}

		return $response;
	}
}
?>