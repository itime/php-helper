<?php

return [
	'defaults' => [
		// 小程序默认配置
		'mini_program' => 'default',

		// 公众号默认配置
		'official_account' => 'default',

		// 微信开放平台默认配置
		'open_platform' => 'default',

		// 企业微信默认配置
		'work' => 'default',

		// 企业微信开放平台默认配置
		'open_work' => 'default',

		/*
		 * 日志配置
		 *
		 * level: 日志级别，可选为：debug/info/notice/warning/error/critical/alert/emergency
		 * file：日志文件位置(绝对路径!!!)，要求可写权限
		 */
		'log' => [
			'level' => env('WECHAT_LOG_LEVEL', 'error'),
			'file' => env('WECHAT_LOG_FILE', runtime_path('logs') . 'wechat.log'),
		],
	],

	// 小程序配置
	'mini_program' => [
		'default' => [
			'app_id' => env('miniprogram.appid', ''),
			'secret' => env('miniprogram.secret', ''),
			'token' => env('miniprogram.token'),
			'aes_key' => env('miniprogram.aes_key', ''),
		],
	],

	// 公众号配置
	'official_account' => [
		'default' => [
			'app_id' => env('official_account.appid', ''),
			'secret' => env('official_account.secret', ''),
			'token' => env('official_account.token', ''),
			'aes_key' => env('official_account.aes_key', ''),
		],
	],

	// 开放平台配置
	'open_platform' => [
		'default' => [
			'app_id' => env('open_platform.appid', ''),
			'secret' => env('open_platform.secret', ''),
			'token' => env('open_platform.token', ''),
			'aes_key' => env('open_platform.aes_key', ''),
		],
	],

	// 企业微信平台配置
	'work' => [
		// 默认应用配置
		'default' => [
			'corp_id' => env('work.corpid', ''),
			'agent_id' => env('work.agent_id', ''),
			'secret' => env('work.secret', ''),
			'token' => env('work.token', ''),
			'aes_key' => env('work.aes_key', ''),
		],

		// 通讯录配置
		'contact' => [
			'corp_id' => env('work.contact_corpid', ''),
			'secret' => env('work.contact_secret', ''),
			'token' => env('work.contact_token', ''),
			'aes_key' => env('work.contact_aes_key', ''),
		],

		// 客户联系配置
		'customer' => [
			'corp_id' => env('work.customer_corpid', ''),
			'secret' => env('work.customer_secret', ''),
			'token' => env('work.customer_token', ''),
			'aes_key' => env('work.customer_aes_key', ''),
		],
	],

	// 企业微信开放平台配置
	'open_work' => [
		'default' => [
			'corp_id' => env('open_work.corpid', ''),
			'secret' => env('open_work.secret', ''),
			'suite_id' => env('open_work.suite_id', ''),
			'suite_secret' => env('open_work.suite_secret', ''),
			'token' => env('open_work.token', ''),
			'aes_key' => env('open_work.aes_key', ''),
			'redirect_uri_install' => env('open_work.redirect_uri_install', ''), // 安装应用的回调url
			'redirect_uri_single' => env('open_work.redirect_uri_single', ''), // 登录回调
			'reg_template_id' => env('open_work.reg_template_id', ''), // 登录回调
		],
	],

];
