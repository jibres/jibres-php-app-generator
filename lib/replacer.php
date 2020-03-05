<?php


class jibresAppReplacer
{
	private static $APP_FOLDER = null;
	public function replaceVar()
	{
		self::$APP_FOLDER = realpath(__DIR__ . '/../../Jibres-AndroidApp');

		// replace app/gradle.properties
		$myAppID = 'com.jibres.'. jibresAppGenerator::store();
		self::gradle('/app/gradle.properties', ['APPLICATION_ID' => $myAppID]);

	}


	private static function store()
	{
		$fileAddr = self::$APP_FOLDER. '/app/src/main/assets/secret/store.txt';
		file_put_contents($fileAddr, jibresAppGenerator::store());
	}

	private static function logo($_logoURL)
	{
		copy($_logoURL, self::$APP_FOLDER. '/app/src/main/res/drawable/logo.png');
	}

	private static function endpoint($jibres_main_app)
	{
		if($jibres_main_app)
		{

		}
		else
		{

		}
	}

	private static function gradle($_file, $_replace)
	{
		$fileAddr = realpath(self::$APP_FOLDER. $_file);

		if(!file_exists($fileAddr))
		{
			jibresAppCode::msg('ini not found! '. $_file, false);
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
}
?>