<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bot;

use Xin\Bot\BotManager;
use Xin\Contracts\Bot\Factory as BotFactory;
use Xin\Thinkphp\Foundation\ServiceProvider;

/**
 * Class HintServiceProvider
 */
class BotServiceProvider extends ServiceProvider {

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
			'hint' => BotFactory::class,
			BotFactory::class => BotManager::class,
			BotManager::class => function () {
				$manager = new BotManager(
					$this->app->config->get('bot')
				);
				$manager->setContainer($this->app);

				return $manager;
			},
		]);
	}

}
