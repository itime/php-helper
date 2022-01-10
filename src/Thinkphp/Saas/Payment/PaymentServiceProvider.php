<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Payment;

use think\Service;
use Xin\Contracts\Saas\Payment\Repository as PaymentRepository;
use Xin\Payment\PaymentManager;
use Xin\Saas\Payment\PaymentManager as SaasPaymentManager;
use Xin\Thinkphp\Saas\Wechat\ConfigProvider;

class PaymentServiceProvider extends Service {

	/**
	 * 启动器
	 */
	public function register() {
		$this->app->bind([
			PaymentManager::class => SaasPaymentManager::class,
			PaymentRepository::class => SaasPaymentManager::class,
			SaasPaymentManager::class => function () {
				return tap(new SaasPaymentManager(
					$this->app->config->get('payment'),
					new ConfigProvider()
				), function (SaasPaymentManager $manager) {
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
	public static function bindAppId($appId) {
		static::$appId = $appId;
	}

}
