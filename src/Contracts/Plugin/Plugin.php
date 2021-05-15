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
	 * @param \Xin\Contracts\Plugin\Factory    $pluginManager
	 * @return boolean
	 */
	public function install(PluginInfo $pluginInfo, Factory $pluginManager);

	/**
	 * 插件卸载
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $pluginManager
	 * @return boolean
	 */
	public function uninstall(PluginInfo $pluginInfo, Factory $pluginManager);

	/**
	 * 插件升级
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $pluginManager
	 * @param string                           $version
	 * @return boolean
	 */
	public function upgrade(PluginInfo $pluginInfo, Factory $pluginManager, $version);

	/**
	 * 启动插件
	 *
	 * @param \Xin\Contracts\Plugin\PluginInfo $pluginInfo
	 * @param \Xin\Contracts\Plugin\Factory    $pluginManager
	 * @return mixed
	 */
	public function boot(PluginInfo $pluginInfo, Factory $pluginManager);
}
