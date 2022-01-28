<?php

namespace Xin\Thinkphp\Repository;

use Xin\Contracts\Repository\Factory as RepositoryFactory;
use Xin\Repository\RepositoryManager;
use Xin\Thinkphp\Foundation\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRepositoryManager();
	}

	/**
	 * 注册
	 * @return void
	 */
	protected function registerRepositoryManager()
	{
		$this->app->bind([
			'repository' => RepositoryFactory::class,
			RepositoryManager::class => function () {
				$config = array_merge(
					[
						'repository' => Repository::class
					],
					$this->app->config->get('repository', [])
				);
				$manager = new RepositoryManager($config);

				$manager->setClassResolver(function ($name) {
					return app($name);
				});

				return $manager;
			},
		]);
	}
}