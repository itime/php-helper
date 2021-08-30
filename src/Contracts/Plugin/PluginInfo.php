<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Plugin;

interface PluginInfo{

	/**
	 * 获取插件名字
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * 获取插件版本
	 *
	 * @return string
	 */
	public function getVersion();

	/**
	 * 检查插件版本
	 *
	 * @param string $newVersion
	 * @return int
	 */
	public function checkVersion($newVersion);

	/**
	 * 获取插件信息
	 *
	 * @return array
	 */
	public function getInfo($name = null);

	/**
	 * 获取配置模板
	 *
	 * @param array $config
	 * @return array
	 */
	public function getConfigTemplate($config = [], $layer = null);

	/**
	 * 获取配置字段类型
	 *
	 * @return array
	 */
	public function getConfigTypeList();

	/**
	 * 当前插件路径
	 *
	 * @param string $path
	 * @return string
	 */
	public function path($path = '');

	/**
	 * 获取插件实例
	 *
	 * @return \Xin\Contracts\Plugin\Plugin
	 */
	public function plugin();
}
