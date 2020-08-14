<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface PluginBoot{
	
	/**
	 * 启动插件
	 *
	 * @return mixed
	 */
	public function boot();
}
