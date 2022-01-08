<?php

namespace Xin\Thinkphp\Wechat;

use Xin\Contracts\Wechat\Factory as WechatFactory;
use Xin\Thinkphp\Foundation\ServiceProvider;
use Xin\Wechat\WechatManager;

class WechatServiceProvider extends ServiceProvider {

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->bind([
			'wechat' => WechatFactory::class,
			WechatFactory::class => WechatManager::class,
			WechatManager::class => function () {
				return new WechatManager(
					$this->app->config->get('wechat')
				);
			},
		]);

		$this->app->bind('wechat.miniprogram', function ($app) {
			return $app['wechat']->miniProgram();
		});

		$this->app->bind('wechat.official_account', function ($app) {
			return $app['wechat']->officialAccount();
		});

		$this->app->bind('wechat.open_platform', function ($app) {
			return $app['wechat']->openPlatform();
		});

		$this->app->bind('wechat.work', function ($app) {
			return $app['wechat']->work();
		});

		$this->app->bind('wechat.open_work', function ($app) {
			return $app['wechat']->openWork();
		});
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		//
	}

}
