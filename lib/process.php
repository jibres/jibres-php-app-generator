<?php

class jibresAppProcess
{
	private static $START_TIME  = null;
	private static $FETCH_TIME  = null;
	private static $STOP_TIME   = null;
	private static $CLEAN_TIME  = null;
	private static $BUILD_TIME  = null;
	private static $FINISH_TIME = null;
	private static $DONE_TIME   = null;
	private static $FAILED_TIME = null;
	private static $PROCESS     = null;


	public static function set($_mode)
	{
		$myTime = microtime(true);
		switch ($_mode)
		{
			case 'start':
				self::$START_TIME = $myTime;
				self::busy(true);
				break;

			case 'fetch':
				self::$FETCH_TIME = $myTime;
				break;

			case 'stop':
				self::$STOP_TIME = $myTime;
				break;

			case 'clean':
				self::$CLEAN_TIME = $myTime;
				break;

			case 'build':
				self::$BUILD_TIME = $myTime;
				break;


			case 'failed':
				self::$FAILED_TIME = $myTime;
				self::busy(false);
				jibresAppOutput::failed();
				break;


			case 'finish':
				self::$FINISH_TIME = $myTime;
				self::busy(false);
				jibresAppOutput::done();
				break;
		}

		return;
	}


	public static function get($_start = null, $_txt = null)
	{
		if(self::$PROCESS)
		{
			jibresAppLog::save(self::$PROCESS, 'process');
			return true;
		}

		// log end process
		$endTime = microtime(true);
		$msg = '';
		$msg .= date("Y-m-d H:i:s");
		$msg .= ' --Total '. round($endTime - self::$START_TIME).'s'. "\t";
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

		self::$PROCESS = $msg;
		return $msg;
	}


	public static function busy($_status = null)
	{
		if(!defined('THIS'))
		{
			define('THIS', realpath(__DIR__. '//../'));
		}
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
				return 1;
			}
			else
			{
				// we are busy from last operation
				jibresAppCode::msg('We are busy from last operation', false);
			}
		}
		else
		{
			file_put_contents($busyFile, 0);
			return 0;
		}
	}
}
?>