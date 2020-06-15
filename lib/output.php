<?php

class jibresAppOutput
{


	public static function done()
	{
		$postData =
		[
			'status'  => 'done',
			'store'   => jibresAppGenerator::store_code(),
			'version' => jibresAppGenerator::apkFileName(),
			'path'    => jibresAppGenerator::path_folder(). jibresAppGenerator::apkFileName(),
			'meta'    => jibresAppProcess::get(),
		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppLog::save($postData, 'Done');
		jibresAppCode::msg($postData);
	}


	public static function failed($_mode = null)
	{
		$postData =
		[
			'status' => 'failed',
			'store'  => jibresAppGenerator::store_code(),
			'version' => jibresAppGenerator::apkFileName(),
			'meta'   => jibresAppProcess::get(). ' *** '. $_mode,

		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);

		// show msg
		jibresAppLog::save($postData, 'Fail');
		jibresAppCode::msg($postData, false);
	}

}
?>