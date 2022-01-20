<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface Factory {

	/**
	 * 插件根路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function rootPath($path = '');

	/**
	 * 插件列表
	 *
	 * @return \Xin\Support\Collection
	 */
	public function plugins();

	/**
	 * 设置过滤器
	 * @param callable $filterCallback
	 */
	public function filter(callable $filterCallback);

	/**
	 * 获取插件目录
	 *
	 * @param string $plugin
	 * @return string
	 */
	public function pluginPath($plugin);

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
	 * @return \Xin\Contracts\Plugin\PluginInfo
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function plugin($plugin);

	/**
	 * 安装插件
	 *
	 * @param string $plugin
	 * @return \Xin\Contracts\Plugin\PluginInfo
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function installPlugin($plugin);

	/**
	 * 卸载插件
	 *
	 * @param string $plugin
	 * @return \Xin\Contracts\Plugin\PluginInfo
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function uninstallPlugin($plugin);

	/**
	 * 启动相关插件
	 */
	public function pluginBoot();

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
	 * 获取配置信息
	 *
	 * @param string $name
	 * @param mixed  $default
	 * @return mixed
	 */
	public function config($name, $default = null);

}
