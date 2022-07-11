<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use think\Service;
use Xin\Contracts\Saas\Wechat\Repository as WechatRepository;
use Xin\Saas\Wechat\WechatManager as SaasWechatManager;
use Xin\Wechat\WechatManager;

class WechatServiceProvider extends Service
{

	/**
	 * @var int
	 */
	protected static $appId = 0;

	/**
	 * 启动器
	 */
	public function register()
	{
		$this->app->bind([
			WechatManager::class => SaasWechatManager::class,
			WechatRepository::class => SaasWechatManager::class,
			SaasWechatManager::class => function () {
				return tap(new SaasWechatManager(
					$this->app->config->get('wechat'),
					new ConfigProvider()
				), function (SaasWechatManager $manager) {
					if (static::$appId) {
						$manager->shouldUseOfAppId(static::$appId);
					}
				});
			},
		]);
	}

	/**
	 * @param int $appId
	 * @return void
	 */
	public static function bindAppId($appId)
	{
		static::$appId = $appId;
	}

}
