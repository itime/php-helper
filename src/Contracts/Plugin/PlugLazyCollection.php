<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface PlugLazyCollection{
	
	/**
	 * 获取插件
	 *
	 * @param string $plugin
	 * @return \Xin\Contracts\Plugin\Plugin
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function plugin($plugin);
	
	/**
	 * 获取列表
	 *
	 * @return array
	 */
	public function lists();
}
