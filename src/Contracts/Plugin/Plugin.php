<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Plugin;

/**
 * Interface PluginInterface
 */
interface Plugin{
	
	/**
	 * 获取插件名字
	 *
	 * @return string
	 */
	public function getName();
	
	/**
	 * 获取插件信息
	 *
	 * @return array
	 */
	public function getInfo();
	
	/**
	 * 获取扩展配置
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getExtraConfig($name);
	
	/**
	 * 当前插件路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function pluginPath($path = '');
	
	/**
	 * 插件安装
	 *
	 * @return boolean
	 */
	public function install();
	
	/**
	 * 插件卸载
	 *
	 * @return boolean
	 */
	public function uninstall();
	
	/**
	 * 启动插件
	 *
	 * @return mixed
	 */
	public function boot();
	
}
