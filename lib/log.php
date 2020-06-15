<?php

class jibresAppLog
{
	public static function save($_data, $_type = null)
	{
		if (!is_dir(THIS. "/tmp/log/"))
		{
			// dir doesn't exist, make it
			mkdir(THIS. "/tmp/log/", 0775, true);
		}
		if($_type === true)
		{
			$fileName = jibresAppGenerator::store(). '-'. jibresAppGenerator::version();
			error_log("\n-------\n".$_data, 3, THIS. "/tmp/log/". $fileName. '.log');
		}
		elseif($_type)
		{
			error_log("\n".$_data, 3, THIS. "/tmp/log/". $_type. '.log');
		}
		else
		{
			error_log("\n".$_data, 3, THIS. "/tmp/log/access.log");
		}
	}
}
?>