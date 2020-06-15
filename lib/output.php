<?php

class jibresAppOutput
{


	public static function done($_store, $_version, $_path)
	{
		$postData =
		[
			'store'   => $_store,
			'status'  => 'done',
			'version' => $_version,
			'path'    => $_path,
			'meta'    => jibresAppCode::process(),
		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);
	}


	public static function failed($_txt = null, $_status)
	{
		$postData =
		[
			'store'  => jibresAppGenerator::store(),
			'status' => 'failed',
			'ok'     => $_status,
			'meta'   => jibresAppCode::process(). ' ***'. $_txt,

		];
		jibresAppExec::send('https://core.jibres.ir/r10/queue/app', true, $postData);
	}
}
?>