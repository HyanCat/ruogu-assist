<?php
/**
 * craw.php
 * ruogu-assist
 *
 * Created by HyanCat on 15/9/18.
 * Copyright (C) 2015 HyanCat. All rights reserved.
 */

return [
	'users' => [
		[
			'uin'    => '291699782',
			'skey'   => env('SKEY_291699782', ''),
			'groups' => [],
		],
		[
			'uin'    => '2656148155',
			'skey'   => env('SKEY_2656148155', ''),
			'groups' => [],
		],
		[
			'uin'    => '1576406713',
			'skey'   => env('SKEY_1576406713', ''),
			'groups' => explode(',', env('GROUP_1576406713', '')),
		],
		[
			'uin'    => '2545804584',
			'skey'   => env('SKEY_2545804584', ''),
			'groups' => [],
		],
	],
];
