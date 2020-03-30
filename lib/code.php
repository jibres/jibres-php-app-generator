<?php


class jibresAppCode
{
	private static $START_TIME = null;
	private static $FETCH_TIME = null;
	private static $CLEAN_TIME = null;
	private static $BUILD_TIME = null;


	public static function boboom($_string = null)
	{
		// change header
		exit($_string);
	}


	public static function msg($_txt = null, $_status)
	{
		self::process(null, $_txt);

		if($_status !== true)
		{
			jibresAppFetcher::failed($_txt, $_status);
		}

		self::jsonBoom(['ok'=> $_status, 'msg'=> [$_txt]]);
	}


	public static function process($_start = null, $_txt = null)
	{
		if($_start)
		{
			switch ($_start)
			{
				case 'start':
					self::$START_TIME = microtime(true);
					break;

				case 'fetch':
					self::$FETCH_TIME = microtime(true);
					break;

				case 'clean':
					self::$CLEAN_TIME = microtime(true);
					break;

				case 'build':
					self::$BUILD_TIME = microtime(true);
					break;
			}
			return;
		}
		else
		{
			self::busy(false);
		}

		// log end process
		$endTime = microtime(true);
		$msg = '';
		$msg .= date("Y-m-d H:i:s");
		$msg .= ' --TotalDiff '. round($endTime - self::$START_TIME).'s'. "\t";
		if(self::$FETCH_TIME)
		{
			$msg .= ' --Fetch '. round(self::$FETCH_TIME - self::$START_TIME).'s'. "\t";
			if(self::$CLEAN_TIME)
			{
				$msg .= ' --Clean '. round(self::$CLEAN_TIME - self::$FETCH_TIME).'s'. "\t";
				if(self::$BUILD_TIME)
				{
					$msg .= ' --Build '. round(self::$BUILD_TIME - self::$CLEAN_TIME).'s'. "\t";
				}
			}
		}

		if(jibresAppGenerator::store())
		{
			$msg .= ' --Store '. jibresAppGenerator::store();
			$msg .= ' --Version '. jibresAppGenerator::version();
		}
		if($_txt)
		{
			$msg .= " -- ". $_txt. " ***";
		}

		jibresAppCode::log($msg, 'process');
	}


	public static function log($_data, $_type = null)
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