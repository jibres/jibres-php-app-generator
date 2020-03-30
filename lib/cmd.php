<?php

class jibresAppCmd
{

	public static function runSH()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./appBuildCmd.jibres.sh';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
	}


	public static function cleanApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew clean';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
		self::$checkSuccess($output);
	}


	public static function buildApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew build';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
		self::$checkSuccess($output);
	}


	public static function releaseApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew assembleRelease';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
		self::$checkSuccess($output);
	}

	private static function checkSuccess($_response)
	{
		if(strpos($_response, 'BUILD SUCCESSFUL in ') !== false)
		{
			$myResponse = str_replace(["\r\n","\n\r","\r"],"\n", $_response);
			$myResponseArr = explode("\n", $myResponse);
			$runSecond = null;
			foreach ($myResponseArr as $key => $myLine)
			{
				if($myStartPos = strpos($myLine, 'BUILD SUCCESSFUL in ') !== false)
				{
					// this is result line
					$runSecond = substr($myLine, $myStartPos);
				}
			}

			var_dump($runSecond);
			var_dump($myResultArr);

			// $startPos = strpos($_response, 'BUILD SUCCESSFUL in ');
			// return true;
		}
		var_dump($myResponse);

		exit();

		// build failed
		return false;
	}
}
?>