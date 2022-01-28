<?php

namespace Xin\Thinkphp\Wechat;

use Xin\Contracts\Wechat\Factory as WechatFactory;
use Xin\Thinkphp\Foundation\ServiceProvider;
use Xin\Wechat\WechatManager;
use Xin\Wechat\WechatMediaManager;
use Xin\Wechat\WechatUserSessionManager;

class WechatServiceProvider extends ServiceProvider
{

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		// 注册微信实例工厂
		$this->registerWechatFactory();

		// 注册微信相关实例
		$this->registerWechatApplications();

		// 注册微信素材管理服务
		$this->registerWechatMediaManager();

		// 注册微信 用户Session 管理服务
		$this->registerWechatUserSessionManager();
	}

	/**
	 * 注册微信实例工厂
	 * @return void
	 */
	protected function registerWechatFactory()
	{
		$this->app->bind([
			'wechat' => WechatFactory::class,
			WechatFactory::class => WechatManager::class,
			WechatManager::class => function () {
				return new WechatManager(
					$this->app->config->get('wechat')
				);
			},
		]);
	}

	/**
	 * 注册微信实例
	 * @return void
	 */
	protected function registerWechatApplications()
	{
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
	 * 注册微信素材服务
	 * @return void
	 */
	protected function registerWechatMediaManager()
	{
		WechatMediaManager::setDefaultCacheResolver(static function () {
			return app('cache');
		});

		$this->app->bind([
			'wechat.media' => WechatMediaManager::class,
			WechatMediaManager::class => function () {
				return new WechatMediaManager(
					$this->app['wechat'],
					$this->app->cache
				);
			},
		]);
	}

	/**
	 * 注册微信 用户Session 管理服务
	 * @return void
	 */
	protected function registerWechatUserSessionManager()
	{
		WechatUserSessionManager::setDefaultCacheResolver(function () {
			return app('cache');
		});

		$this->app->bind([
			'wechat.user_session' => WechatUserSessionManager::class,
			WechatUserSessionManager::class => function () {
				return new WechatUserSessionManager(
					$this->app->cache
				);
			},
		]);
	}

}
