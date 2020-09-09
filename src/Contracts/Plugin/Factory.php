<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface Factory{
	
	/**
	 * 插件是否存在
	 *
	 * @param string $plugin
	 * @return bool
	 */
	public function has($plugin);
	
	/**
	 * 获取插件
	 *
	 * @param string $plugin
	 * @return \Xin\Contracts\Plugin\Plugin
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function plugin($plugin);
	
	/**
	 * 获取插件下的类路径
	 *
	 * @param string $plugin
	 * @param string $class
	 * @return string
	 */
	public function pluginClass($plugin, $class);
	
	/**
	 * 获取插件下的控制器路径
	 *
	 * @param string $plugin
	 * @param string $controller
	 * @param string $layer
	 * @return string
	 */
	public function controllerClass($plugin, $controller, $layer = 'controller');
	
	/**
	 * 插件列表
	 *
	 * @return array
	 */
	public function lists();
	
	/**
	 * 启动所有插件
	 */
	public function boot();
	
	/**
	 * 调用插件操作
	 *
	 * @param mixed  $request
	 * @param string $plugin
	 * @param string $controller
	 * @param string $action
	 * @return mixed
	 */
	public function invoke($request, $plugin, $controller, $action);
	
	/**
	 * 插件根路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function rootPath($path = '');
	
	/**
	 * 获取插件目录
	 *
	 * @param string $plugin
	 * @return string
	 */
	public function path($plugin);
	
}
