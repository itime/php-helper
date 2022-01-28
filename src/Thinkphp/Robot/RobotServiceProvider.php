<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Robot;

use Xin\Contracts\Robot\Factory as RobotFactory;
use Xin\Robot\RobotManager;
use Xin\Thinkphp\Foundation\ServiceProvider;

/**
 * Class RobotServiceProvider
 */
class RobotServiceProvider extends ServiceProvider
{

	/**
	 * @inheritDoc
	 */
	public function register()
	{
		$this->registerManager();
	}

	/**
	 * 注册提示管理器
	 * @return void
	 */
	protected function registerManager()
	{
		$this->app->bind([
			'robot' => RobotFactory::class,
			RobotFactory::class => RobotManager::class,
			RobotManager::class => function () {
				$manager = new RobotManager(
					$this->app->config->get('robot')
				);
				$manager->setContainer($this->app);

				return $manager;
			},
		]);
	}

}
