<?php

class jibresAppLog
{
	public static function save($_data, $_title = null, $_seperator = '*')
	{
		$logFolder = THIS. "/tmp/log/";
		if (!is_dir($logFolder))
		{
			// dir doesn't exist, make it
			mkdir($logFolder, 0775, true);
		}

		$fileName = jibresAppGenerator::store(). '-'. jibresAppGenerator::version();
		$logFile = jibresAppGenerator::path_loc(). jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(true);

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

		error_log($myData, 3, $logFile. '.log');
	}
}
?>