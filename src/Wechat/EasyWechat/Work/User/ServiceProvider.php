<?php

namespace Xin\Wechat\EasyWechat\Work\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{

	/**
	 * {@inheritdoc}.
	 */
	public function register(Container $app)
	{
		$app['user'] = function ($app) {
			return new Client($app);
		};
	}

}
