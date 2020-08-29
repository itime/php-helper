<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use Xin\Support\Hasher;

/**
 * Class HintServiceProvider
 */
class HashServiceProvider extends ServiceProvider{
	
	/**
	 * 启动器
	 */
	public function register(){
		$this->app->bind('hash', function(){
			return new Hasher();
		});
	}
	
}
