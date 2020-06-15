<?php

class jibresAppLog
{
	public static function save($_data, $_type = null)
	{
		$logFolder = THIS. "/tmp/log/";
		if (!is_dir($logFolder))
		{
			// dir doesn't exist, make it
			mkdir($logFolder, 0775, true);
		}
		if($_type === true)
		{
			$fileName = jibresAppGenerator::store(). '-'. jibresAppGenerator::version();
			$logFile = jibresAppGenerator::path_loc(). jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(true);
			error_log("\n-------\n".$_data, 3, $logFile. '.log');
		}
		elseif($_type)
		{
			error_log("\n".$_data, 3, $logFolder. $_type. '.log');
		}
		else
		{
			error_log("\n".$_data, 3, $logFolder. "access.log");
		}
	}
}
?>