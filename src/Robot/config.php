<?php
// +----------------------------------------------------------------------
// | 机器人设置
// +----------------------------------------------------------------------

return [
	// 定义相关默认配置
	'defaults' => [
		'robot' => env('robot.driver', 'default'),
	],

	// 定义机器人的相关配置
	'robots' => [
		'default' => [
			'driver' => 'qywork',
			'key' => env('robot.qywork_key', ''),
		],

		//
		'danger' => [
			'driver' => 'dingtalk',
			'key' => env('robot.dingtalk_key', ''),
			'secret' => env('robot.dingtalk_secret', ''),
			'title' => env('robot.dingtalk_title', '提醒助手'),
		],
	],
];
