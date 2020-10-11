<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

class WechatServiceProvider extends ServiceProvider{
	
	/**
	 * 启动器
	 */
	public function register(){
		$this->app->bind('wechat', function(){
			return new Wechat(
				$this->app->config->get('wechat')
			);
		});
	}
}
