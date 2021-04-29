<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface Plugin{

	/**
	 * 插件安装
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $factory
	 * @return boolean
	 */
	public function install(PluginInfo $pluginInfo, Factory $factory);

	/**
	 * 插件卸载
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $factory
	 * @return boolean
	 */
	public function uninstall(PluginInfo $pluginInfo, Factory $factory);

	/**
	 * 启动插件
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $factory
	 * @return mixed
	 */
	public function boot(PluginInfo $pluginInfo, Factory $factory);
}
