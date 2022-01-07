<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Console;

use think\Service;

class ConsoleServiceProvider extends Service {

	/**
	 * 启动器
	 */
	public function boot() {
		if (!$this->app->runningInConsole()) {
			return;
		}

		$this->commands([
			KeyGenerate::class,
		]);
	}

}
