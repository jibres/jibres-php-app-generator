<?php


class jibresAppCode
{
	public static function boboom($_string = null)
	{
		// change header
		exit($_string);
	}


	public static function msg($_txt = null, $_status)
	{
		jibresAppFetcher::failed($_txt, $_status);
		self::jsonBoom(['ok'=> $_status, 'msg'=> [$_txt]]);
	}


	public static function log($_data, $_type = null)
	{
		if($_type)
		{
			error_log($_data, 0, THIS. "/tmp/log/". $_type. ".log");
		}
		else
		{
			error_log($_data, 0, THIS. "/tmp/log/access.log");
		}
	}

	public static function busy($_status = null)
	{
		$busyFile   = THIS.'/busy.conf';
		if(!file_exists($busyFile))
		{
			// define on start
			file_put_contents($busyFile, 0);
		}

		$busyStatus = (int) file_get_contents($busyFile);
		$newStatus  = null;

		if($_status === true)
		{
			if($busyStatus === 0)
			{
				// if it's free
				// change it to busy
				file_put_contents($busyFile, 1);
			}
			else
			{
				// we are busy from last operation
				jibresAppCode::msg('We are busy from last operation', true);
			}
		}
		else
		{
			file_put_contents($busyFile, 0);
		}
	}


	public static function jsonBoom($_result = null)
	{
		self::busy(false);

		if(is_array($_result))
		{
			$_result = json_encode($_result, JSON_UNESCAPED_UNICODE);
		}

		if(substr($_result, 0, 1) === "{")
		{
			@header("Content-Type: application/json; charset=utf-8");
		}
		echo $_result;
		self::boboom();
	}
}
?>