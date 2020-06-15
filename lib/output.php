<?php

class jibresAppOutput
{


	public static function done()
	{
		jibresAppProcess::set('done');
		$postData =
		[
			'store'   => jibresAppGenerator::store(),
			'status'  => 'done',
			'version' => jibresAppGenerator::apkFileName(),
			'path'    => jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(),
			'meta'    => jibresAppProcess::get(),
		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppCode::msg($postData);
	}


	public static function failed()
	{
		jibresAppProcess::set('failed');
		$postData =
		[
			'store'  => jibresAppGenerator::store(),
			'status' => 'failed',
			'ok'     => false,
			'meta'   => jibresAppProcess::get(). ' *** BUILD FAILED!',

		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppCode::msg($postData, false);
	}
}
?>