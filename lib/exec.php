<?php

class jibresAppExec
{
	public static function send($_url, $_json = null, $_postData = null)
	{
		$ch = curl_init();

		if ($ch === false)
		{
			jibresAppProcess::set('curlFailed');
			jibresAppCode::msg('Curl failed to initialize', false);
		}

		curl_setopt($ch, CURLOPT_URL, $_url);
		// turn on some setting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, false);
		// turn off some setting
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// set agent
		curl_setopt($ch, CURLOPT_USERAGENT, 'JibresBot (like TwitterBot)');
		// timeout setting
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		if($_postData)
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $_postData);
		}

		$response = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$myResponse = null;
		$myResult = null;
		// error on result
		if ($response === false)
		{
			jibresAppProcess::set('curlError');
			jibresAppCode::msg(curl_error($ch). ':'. curl_errno($ch), false);
		}
		// empty result
		if (empty($response) || is_null($response) || !$response)
		{
			jibresAppProcess::set('curlEmpty');
			jibresAppCode::msg('Empty server response', false);
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