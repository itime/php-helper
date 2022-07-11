<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use Xin\Contracts\Hint\Factory as HintFactory;
use Xin\Hint\Hint;
use Xin\Hint\HintManager;
use Xin\Support\Str;
use Xin\Thinkphp\Foundation\ServiceProvider;

/**
 * Class HintServiceProvider
 */
class HintServiceProvider extends ServiceProvider
{

	/**
	 * @inheritDoc
	 */
	public function register()
	{
		$this->registerManager();
		$this->registerScenes();
	}

	/**
	 * 注册提示管理器
	 * @return void
	 */
	protected function registerManager()
	{
		$this->app->bind([
			'hint' => HintFactory::class,
			HintFactory::class => HintManager::class,
			HintManager::class => function () {
				$manager = new HintManager(
					$this->app->config->get('hint')
				);
				$manager->setContainer($this->app);

				return $manager;
			},
		]);
	}

	/**
	 * 注册提示器场景
	 */
	protected function registerScenes()
	{
		/** @var HintManager $manager */
		$manager = $this->app['hint'];

		// extend api hint
		$manager->extend('api', function ($name, $config) {
			return new Hint($config, new ApiHandler());
		});

		// extend web hint
		$manager->extend('web', function ($name, $config) {
			return new Hint($config, new WebHandler());
		});

		// set auto bind hint
		$manager->setAutoResolver(function () {
			return $this->getScene();
		});
	}

	/**
	 * @return string
	 */
	protected function getScene()
	{
		return $this->isApiRequest() ? "api" : "web";
	}

	/**
	 * @return bool
	 */
	protected function isApiRequest()
	{
		return $this->app->request->isAjax() ||
			$this->app->request->isJson() ||
			Str::endsWith($this->app->http->getName(), 'api');
	}

}
