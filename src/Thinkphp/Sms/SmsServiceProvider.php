<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Sms;

use Xin\Contracts\Sms\Factory as SmsFactory;
use Xin\Sms\SmsManager;
use Xin\Thinkphp\Foundation\ServiceProvider;

/**
 * Class SmsServiceProvider
 */
class SmsServiceProvider extends ServiceProvider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->registerManager();
	}

	/**
	 * 注册提示管理器
	 * @return void
	 */
	protected function registerManager() {
		$this->app->bind([
			'sms' => SmsFactory::class,
			SmsFactory::class => SmsManager::class,
			SmsManager::class => function () {
				$manager = new SmsManager(
					$this->app->config->get('sms')
				);
				$manager->setContainer($this->app);

				return $manager;
			},
		]);
	}

}
