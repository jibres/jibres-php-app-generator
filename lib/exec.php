<?php

class jibresAppExec
{
	public static function send($_url, $_json = null, $_postData = null)
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