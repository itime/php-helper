<?php

namespace Xin\Wechat\EasyWechat\Work\Corp;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritdoc}.
	 */
	public function register(Container $app) {
		$app['corp'] = function ($app) {
			return new Client($app);
		};
	}

}
