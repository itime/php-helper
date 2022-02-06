<?php
// +----------------------------------------------------------------------
// | 短信设置
// +----------------------------------------------------------------------

return [
	// HTTP 请求的超时时间（秒）
	'timeout' => 5.0,

	// 默认配置
	'defaults' => [
		'channel' => env('sms.channel', 'aliyun'),

		// EasySms
		// 网关调用策略，默认：顺序调用
		'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

		// 默认可用的发送网关
		'gateways' => [
			'yunpian', 'aliyun',
		],
	],

	// 定义短信渠道实现器
	'channels' => [
		// 阿里云
		'aliyun' => [
			'access_key_id' => env('sms.aliyun_access_key_id'),
			'access_key_secret' => env('sms.aliyun_access_key_secret'),
			'sign_name' => env('sms.aliyun_sign_name'),
		],
	],
];
