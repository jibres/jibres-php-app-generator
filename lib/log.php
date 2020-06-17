<?php

class jibresAppLog
{
	public static function save($_data, $_title = null, $_seperator = '*')
	{
		// $logFolder = THIS. "/tmp/log/";
		// $fileName = jibresAppGenerator::store(). '-'. jibresAppGenerator::version();

		$logFolder = jibresAppGenerator::path_loc(). jibresAppGenerator::path_folder();
		// create log folder if not exist
		self::create_folder($logFolder);
		// create file addr
		$logFile = $logFolder. jibresAppGenerator::apkFileName(true). '.log';

		$myData = "";
		if($_title)
		{

			// $myData .= "-------\n";
			$myData .= str_repeat($_seperator, 30);
			$myData .= ' '. date('Y-m-d H:i:s'). ' ';
			$myData .= " ". $_title. "\n";
		}
		if($_data)
		{
			if(is_array($_data))
			{
				$myData = json_encode($_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			}
			else
			{
				$myData .= $_data;
			}
		}

		error_log($myData, 3, $logFile);
	}

	private static function create_folder($_path)
	{
		if (!is_dir($_path))
		{
			// dir doesn't exist, make it
			mkdir($_path, 0774, true);
		}
	}

}
?>