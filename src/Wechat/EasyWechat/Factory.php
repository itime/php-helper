<?php

namespace Xin\Wechat\EasyWechat;


use Xin\Support\Str;

/**
 * Class Factory.
 * @method static \Xin\Wechat\EasyWechat\Payment\Application            payment(array $config)
 * @method static \Xin\Wechat\EasyWeChat\MiniProgram\Application        miniProgram(array $config)
 * @method static \Xin\Wechat\EasyWeChat\OpenPlatform\Application       openPlatform(array $config)
 * @method static \Xin\Wechat\EasyWeChat\OfficialAccount\Application    officialAccount(array $config)
 * @method static \Xin\Wechat\EasyWeChat\BasicService\Application       basicService(array $config)
 * @method static \Xin\Wechat\EasyWeChat\Work\Application               work(array $config)
 * @method static \Xin\Wechat\EasyWeChat\OpenWork\Application           openWork(array $config)
 * @method static \Xin\Wechat\EasyWeChat\MicroMerchant\Application      microMerchant(array $config)
 */
class Factory extends \EasyWeChat\Factory {

	/**
	 * @inheritDoc
	 */
	public static function make($name, array $config) {
		$namespace = Str::studly($name);
		dd(__NAMESPACE__);
		$application = "\\App\\Services\\WeChat\\{$namespace}\\Application";

		return new $application($config);
	}

	/**
	 * Dynamically pass methods to the application.
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments) {
		return static::make($name, ...$arguments);
	}

}
