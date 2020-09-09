<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Xin\Contracts\Plugin\Factory as PluginFactory;

trait InteractsPluginBoot{
	
	/**
	 * 启动插件
	 */
	protected function pluginBoot(){
		/** @var PluginFactory $pm */
		$pm = app('PluginManager');
		$pm->boot();
	}
}
