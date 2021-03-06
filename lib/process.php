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
		jibresAppLog::save(null, 'PROCESS '. $_mode, '/');
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
			case 'failedError':
				self::$FAILED_TIME = $myTime;
				self::busy(false);
				break;


			case 'finish':
				self::$FINISH_TIME = $myTime;
				self::busy(false);
				jibresAppOutput::done($_mode);
				break;


			// info conditions
			case 'emptyQueue':
				self::busy(false);
				jibresAppOutput::info($_mode);
				break;


			// error conditions
			case 'curlFailed':
			case 'curlError':
			case 'curlEmpty':
			case 'apkNotExist':
			case 'depTitleNotString':
			case 'depTitleNotSet':
			case 'depLogoNotPNG':
			case 'depLogoNotSet':
			case 'storeNotExist':
			case 'apkNotExist':
			case 'copySourceNotExist':
			case 'getHeaderFalse':
			case 'iniNotFound':
				self::busy(false);
				jibresAppOutput::error($_mode);
				break;


			default:
				break;
		}

		return;
	}


	public static function get()
	{
		if(self::$PROCESS)
		{
			jibresAppLog::save(self::$PROCESS, 'PROCESS');
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