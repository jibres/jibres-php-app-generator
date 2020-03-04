<?php


class jibresAppReplacer
{
	public function replaceVar()
	{
		// replace app/gradle.properties
		$myAppID = 'com.jibres.'. jibresAppGenerator::store();
		self::replace_ini('/app/gradle.properties', ['APPLICATION_ID' => $myAppID]);

	}

	private static function replace_logo()
	{

	}

	private static function replace_endpoint($jibres_main_app)
	{
		if($jibres_main_app)
		{

		}
		else
		{

		}
	}

	private static function replace_ini($_file, $_replace)
	{
		$fileAddr = realpath(APP_FOLDER. $_file);

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