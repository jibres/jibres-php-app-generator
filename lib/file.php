<?php

class jibresAppFile
{
	public static function copyAPK($_to)
	{
		$factoryAPK = APP_FOLDER. '/app/build/outputs/apk/release/app-release.apk';


		if(file_exists($factoryAPK))
		{
			$myDestDir = dirname($_to);
			if (!is_dir($myDestDir))
			{
				// dir doesn't exist, make it
				mkdir($myDestDir, 0775, true);
			}
			// copy file
			copy($factoryAPK, $_to);

			// remove from app creator to prevent bug of compile error, use old app
			unlink($factoryAPK);
		}
		else
		{
			jibresAppProcess::set('apkNotExist');
			jibresAppCode::msg('APK file is not exist! '. $factoryAPK. ' - '. $_to, true);
		}
	}
}
?>