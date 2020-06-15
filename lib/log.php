<?php

class jibresAppLog
{
	public static function save($_data, $_title = true)
	{
		$logFolder = THIS. "/tmp/log/";
		if (!is_dir($logFolder))
		{
			// dir doesn't exist, make it
			mkdir($logFolder, 0775, true);
		}

		$fileName = jibresAppGenerator::store(). '-'. jibresAppGenerator::version();
		$logFile = jibresAppGenerator::path_loc(). jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(true);

		$myData = "\n-------\n";
		if($_title)
		{
			$myData .= str_repeat('*', 50). " ". $_title. "\n";

		}
		$myData .= $_data;

		error_log($myData, 3, $logFile. '.log');

	}
}
?>