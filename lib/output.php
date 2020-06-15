<?php

class jibresAppOutput
{


	public static function done()
	{
		$postData =
		[
			'status'  => 'done',
			'store'   => jibresAppGenerator::store(),
			'version' => jibresAppGenerator::apkFileName(),
			'path'    => jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(),
			'meta'    => jibresAppProcess::get(),
		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppCode::msg($postData);
	}


	public static function failed($_mode = null)
	{
		$postData =
		[
			'status' => 'failed',
			'store'  => jibresAppGenerator::store(),
			'version' => jibresAppGenerator::apkFileName(),
			'meta'   => jibresAppProcess::get(). ' *** '. $_mode,

		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppCode::msg($postData, false);
	}
}
?>