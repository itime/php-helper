<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\App;
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Thinkphp\Foundation\ServiceProvider;

class PluginServiceProvider extends ServiceProvider{
	
	/**
	 * 注册插件管理器
	 */
	public function register(){
		$this->app->bind("PlugManager", PluginFactory::class);
		$this->app->bind(PluginFactory::class, function(App $app){
			return new PluginManager($app, array_merge([
				'default' => [
					'app_name' => 'admin',
				],
				
				'namespace' => 'plugin',
				'path'      => root_path('plugin'),
			], config('plugin')));
		});
	}
}
