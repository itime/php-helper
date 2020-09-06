<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Xin\Thinkphp\Foundation\ServiceProvider;

class PluginServiceProvider extends ServiceProvider{
	
	/**
	 * 注册插件管理器
	 */
	public function register(){
		$this->app->bind("PlugManager", function($app){
			return new PluginManager($app, config('plugin'));
		});
	}
}
