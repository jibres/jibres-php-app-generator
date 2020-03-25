<?php

class jibresAppFetcher
{
	public static function run()
	{
		// get store id
		$myStore = self::get_api_data('https://core.jibres.com/r10/queue/app', true);
		$endPoint = null;

		// get store id
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			jibresAppGenerator::STORE($myStore['result']['store']);
			$endPoint = jibresAppReplacer::endpoint(true, $myStore['result']['store']);
		}
		elseif(isset($myStore['result']['jibres']) && $myStore['result']['jibres'] === true)
		{
			jibresAppGenerator::STORE(null);
			$endPoint = jibresAppReplacer::endpoint(null);
		}
		else
		{
			jibresAppCode::msg('Queue is empty', true);
		}

		// get store build version
		if(isset($myStore['result']['build']) && is_numeric($myStore['result']['build']))
		{
			jibresAppGenerator::$VERSION_BUILD = $myStore['result']['build'];
		}

		// get store api android
		$android_api = self::get_api_data($endPoint. '/android', true);

		// set title
		if(isset($android_api['result']['title']))
		{
			$myTitle = $android_api['result']['title'];
			if(is_string($myTitle))
			{
				jibresAppReplacer::appName($myTitle);
			}
			else
			{
				// we need png logo
				jibresAppCode::msg('Title is not string!', false);
			}
		}
		else
		{
			jibresAppCode::msg('Title is not set!', false);
		}

		// set logo
		if(isset($android_api['result']['logo']['icon']))
		{
			$myLogo = $android_api['result']['logo']['icon'];
			if(is_string($myLogo) && substr($myLogo, -3) === 'png')
			{
				jibresAppReplacer::logo($myLogo);
			}
			else
			{
				// we need png logo
				jibresAppCode::msg('Logo is not in PNG format!', false);
			}
		}
		else
		{
			jibresAppCode::msg('Logo is not set!', false);
		}

		jibresAppGenerator::apiData($android_api);

		// get store api android intro
		$android_intro = self::get_api_data($endPoint. '/android/intro', true);
		if(isset($android_intro['result']))
		{
			$myIntro = $android_intro['result'];
			$myIntro = json_encode($myIntro, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			jibresAppReplacer::fill('/app/src/main/assets/json/intro_default.json', $myIntro);
		}

		// get store api android splash
		$android_splash = self::get_api_data($endPoint. '/android/splash', true);
		if(isset($android_splash['result']))
		{
			$mySplash = $android_splash['result'];
			$mySplash = json_encode($mySplash, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			jibresAppReplacer::fill('/app/src/main/assets/json/splash_default.json', $mySplash);
		}
	}


	public static function done($_store, $_version, $_path)
	{
		$postData =
		[
			'store'   => $_store,
			'status'  => 'done',
			'version' => $_version,
			'meta'    => ['path' => $_path],

		];
		self::get_api_data('https://core.jibres.com/r10/queue/app', true, $postData);
	}


	public static function failed($_txt = null, $_status)
	{
		$postData =
		[
			'store'  => jibresAppGenerator::store(),
			'status' => 'inprogress',
			'ok'     => $_status,
			'meta'   => $_txt,

		];
		self::get_api_data('https://core.jibres.com/r10/queue/app', true, $postData);
	}


	private static function get_api_data($_url, $_json = null, $_postData = null)
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