<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\Service;
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Plugin\PluginManager;

class PluginServiceProvider extends Service {

	/**
	 * 注册插件管理器
	 */
	public function register() {
		$this->registerPluginManager();

		$this->registerMiddleware();
	}

	/**
	 * 启动程序
	 */
	public function boot() {
		$this->app->event->listen('HttpRun', function () {
			$this->app->bind([
				'think\route\Url' => Url::class,
			]);
		});
	}

	/**
	 * 注册插件管理器
	 */
	protected function registerPluginManager() {
		$this->app->bind("pluginManager", PluginFactory::class);
		$this->app->bind(PluginFactory::class, function () {
			return new PluginManager(array_merge([
				'default' => [
					'app_name' => 'admin',
				],

				'namespace' => 'plugin',
				'path' => root_path('plugin'),
			], config('plugin')));
		});
	}

	/**
	 * 注册中间件
	 */
	protected function registerMiddleware() {
		$this->app->event->listen('HttpRun', function () {
			$this->app->middleware->add(function ($request, \Closure $next) {
				/** @var PluginFactory $pm */
				$pm = $this->app['pluginManager'];
				$pm->pluginBoot();

				return $next($request);
			});
		});
	}

}
