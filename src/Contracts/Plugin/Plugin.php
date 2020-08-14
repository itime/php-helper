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
	 * 获取插件信息
	 *
	 * @return array
	 */
	public function getPluginInfo();

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

}
