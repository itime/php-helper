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
	 * 注册服务
	 * @return void
	 */
	public function register() {
		$this->registerPluginManager();
	}

	/**
	 * @return void
	 */
	public function boot() {
		$this->registerConsole();

		$this->registerUrl();

		$this->registerMiddleware();
	}

	/**
	 * 注册插件管理器
	 * @return void
	 */
	protected function registerPluginManager() {
		$this->app->bind("pluginManager", PluginFactory::class);
		$this->app->bind(PluginFactory::class, function () {
			return new PluginManager(array_merge([
				'default' => [
					'app_name' => 'admin',
				],
				'namespace' => 'plugins',
				'path' => root_path('plugins'),
			], config('plugin')));
		});
	}

	/**
	 * 注册命令行
	 * @return void
	 */
	protected function registerConsole() {
		if (!$this->app->runningInConsole()) {
			return;
		}

		/** @var PluginFactory $factory */
		$factory = $this->app->make('pluginManager');

		$tasks = $commands = [];
		/** @var \Xin\Plugin\PluginInfo $pluginInfo */
		foreach ($factory->plugins() as $pluginInfo) {
			$pluginCommands = $pluginInfo->getInfo('commands');
			if (!empty($pluginCommands)) {
				$commands = array_merge($commands, $pluginCommands);
			}

			$pluginTasks = $pluginInfo->getInfo('tasks');
			if (!empty($pluginTasks)) {
				$tasks = array_merge($tasks, $pluginTasks);
			}
		}

		// 挂载命令行
		$this->attachCommands($commands);

		// 挂载计划任务
		$this->attachTasks($tasks);
	}

	/**
	 * 挂载命令行
	 * @param string[] $commands
	 * @return void
	 */
	protected function attachCommands($commands) {
		$commands = array_unique($commands);
		$this->commands($commands);
	}

	/**
	 * 挂载计划任务
	 * @param string[] $tasks
	 * @return void
	 */
	protected function attachTasks($tasks) {
		$cronConfig = $this->app->config->get('cron');

		$tasks = array_merge($cronConfig['tasks'], $tasks);
		$cronConfig['tasks'] = array_unique($tasks);

		$this->app->config->set($cronConfig, 'cron');
	}

	/**
	 * 注册Url类
	 * @return void
	 */
	protected function registerUrl() {
		$this->app->event->listen('HttpRun', function () {
			$this->app->bind([
				'think\route\Url' => Url::class,
			]);
		});
	}

	/**
	 * 注册中间件
	 * @return void
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
