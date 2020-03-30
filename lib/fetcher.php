<?php

class jibresAppFetcher
{
	public static function run()
	{
		// get store id
		$myStore = jibresAppExec::send('https://core.jibres.com/r10/queue/app', true);
		$endPoint = null;

		// get store id
		if(isset($myStore['result']['store']) && $myStore['result']['store'])
		{
			$myStoreID = $myStore['result']['store'];
			jibresAppGenerator::STORE($myStoreID);
			$endPoint = jibresAppReplacer::endpoint(true, $myStoreID);
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
		$android_api = jibresAppExec::send($endPoint. '/android', true);

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
		$android_intro = jibresAppExec::send($endPoint. '/android/intro', true);
		if(isset($android_intro['result']))
		{
			$myIntro = $android_intro['result'];
			$myIntro = json_encode($myIntro, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			jibresAppReplacer::fill('/app/src/main/assets/json/intro_default.json', $myIntro);
		}

		// get store api android splash
		$android_splash = jibresAppExec::send($endPoint. '/android/splash', true);
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
			'path'    => $_path,
			'meta'    => jibresAppCode::process(),
		];
		jibresAppExec::send('https://core.jibres.com/r10/queue/app', true, $postData);
	}


	public static function failed($_txt = null, $_status)
	{
		$postData =
		[
			'store'  => jibresAppGenerator::store(),
			'status' => 'inprogress',
			'ok'     => $_status,
			'meta'   => jibresAppCode::process(). ' ***'. $_txt,

		];
		jibresAppExec::send('https://core.jibres.com/r10/queue/app', true, $postData);
	}
}
?>