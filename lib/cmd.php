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
	}


	public static function buildApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew build';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
	}


	public static function releaseApp()
	{
		$cmd_runGradle = 'cd '.APP_FOLDER. ' && ./gradlew assembleRelease';
		$output = shell_exec($cmd_runGradle);
		jibresAppCode::log($output, true);
	}
}
?>