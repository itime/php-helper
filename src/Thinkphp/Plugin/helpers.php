<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

use Xin\Thinkphp\Facade\Plugin;

if(!function_exists('plugin_has')){
	/**
	 * 插件是否存在
	 *
	 * @param string $plugin
	 * @return bool
	 */
	function plugin_has($plugin){
		return Plugin::has($plugin);
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
		return Plugin::path($plugin);
	}
}

if(!function_exists('plugin_invoke')){
	/**
	 * 调用插件操作
	 *
	 * @param mixed  $request
	 * @param string $plugin
	 * @param string $controller
	 * @param string $action
	 * @return mixed
	 */
	function plugin_invoke(\think\Request $request, $plugin, $controller, $action){
		return Plugin::invoke($request, $plugin, $controller, $action);
	}
}

if(!function_exists('plugin_url')){
	/**
	 * 生成插件url
	 *
	 * @param string $url
	 * @param array  $vars
	 * @param bool   $suffix
	 * @param bool   $domain
	 * @return \think\route\Url
	 */
	function plugin_url(string $url = '', array $vars = [], $suffix = true, $domain = false){
		$url = strpos($url, '>') ? $url : request()->plugin().">".$url;
		return url($url, $vars, $suffix, $domain);
	}
}
