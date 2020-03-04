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
		self::jsonBoom(['ok'=> $_status, 'result'=> $_txt]);
	}


	public static function jsonBoom($_result = null)
	{
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