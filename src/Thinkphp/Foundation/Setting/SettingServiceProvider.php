<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Setting;

use think\Service;
use Xin\Thinkphp\Foundation\Setting\Command\Clear;
use Xin\Thinkphp\Foundation\Setting\Command\Show;
use Xin\Thinkphp\Foundation\Setting\Command\Update;

class SettingServiceProvider extends Service {

	/**
	 * @inheritDoc
	 */
	public function register() {
		if ($this->app->runningInConsole()) {
			$this->app->event->listen('AppInit', function () {
				$initializersRef = new \ReflectionProperty($this->app, 'initializers');
				$initializersRef->setAccessible(true);
				$initializers = $initializersRef->getValue($this->app);
				$initializers[] = SettingServiceProvider::class;
				$initializersRef->setValue($this->app, $initializers);
			});
		}
	}

	/**
	 * @inheritDoc
	 */
	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->commands([
				Show::class,
				Clear::class,
				Update::class,
			]);
		} else {
			$this->app->event->listen('HttpRun', function () {
				DatabaseSetting::refreshThinkConfig();
			});
		}
	}

	/**
	 * @throws \ReflectionException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function init() {
		DatabaseSetting::refreshThinkConfig();
	}

}
