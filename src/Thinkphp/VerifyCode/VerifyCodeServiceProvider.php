<?php

namespace Xin\Thinkphp\VerifyCode;

use Xin\Contracts\VerifyCode\Factory as VerifyCodeFactory;
use Xin\Support\Arr;
use Xin\Thinkphp\Foundation\ServiceProvider;
use Xin\VerifyCode\VerifyCodeManager;

class VerifyCodeServiceProvider extends ServiceProvider
{
	/**
	 * @return void
	 */
	public function register(): void
	{
		$this->registerVerifyCodeManager();
	}

	/**
	 * 注册验证码服务
	 * @return void
	 */
	protected function registerVerifyCodeManager()
	{
		$this->app->bind([
			'sms' => VerifyCodeFactory::class,
			VerifyCodeFactory::class => VerifyCodeManager::class,
			VerifyCodeManager::class => function () {
				$manager = new VerifyCodeManager(
					$this->app->config->get('verify_code')
				);
				$manager->setContainer($this->app);

				$this->registerStores($manager);

				return $manager;
			},
		]);
	}

	/**
	 * 注册验证码存储器
	 * @param VerifyCodeManager $manager
	 * @return void
	 */
	protected function registerStores($manager)
	{
		$manager->setStoreResolver(function ($config) {
			if (Arr::get($config, 'model')) {
				return new ModelStoreProvider($config);
			}

			return new DatabaseStoreProvider($config);
		});
	}
}