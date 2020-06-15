<?php


class jibresAppCode
{
	public static function msg($_txt = null, $_status = true)
	{
		self::jsonBoom(['ok'=> $_status, 'msg'=> [$_txt]]);
	}


	private static function jsonBoom($_result = null)
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


	private static function boboom($_string = null)
	{
		// change header
		exit($_string);
	}
}
?>