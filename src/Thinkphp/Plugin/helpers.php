<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

use Xin\Thinkphp\Plugin\PluginManager;

if(!function_exists('plugin_has')){
	/**
	 * 插件是否存在
	 *
	 * @param string $plugin
	 * @return bool
	 */
	function plugin_has($plugin){
		return PluginManager::has($plugin);
	}
}

if(!function_exists('plugin_path')){
	/**
	 * 获取插件目录
	 *
	 * @param string $plugin
	 * @return string
	 */
	function plugin_path($plugin){
		return PluginManager::path($plugin);
	}
}
