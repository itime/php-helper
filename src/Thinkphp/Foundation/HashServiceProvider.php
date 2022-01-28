<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use think\Service;
use Xin\Hashing\HashManager;

/**
 * Class HintServiceProvider
 */
class HashServiceProvider extends Service
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('hash', function () {
			return new HashManager($this->app);
		});

		$this->app->bind('hash.driver', function () {
			return $this->app['hash']->driver();
		});
	}

}
