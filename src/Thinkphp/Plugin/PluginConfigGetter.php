<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Xin\Support\Arr;

trait PluginConfigGetter {

	/**
	 * @var string
	 */
	protected $pluginName = '';

	/**
	 * 获取插件配置
	 *
	 * @param string $name
	 * @param null   $default
	 * @return mixed
	 */
	protected function config($name, $default = null) {
		$config = DatabasePlugin::getPluginConfig($this->getPluginName());

		return Arr::get($config, $name, $default);
	}

	/**
	 * 获取插件名称
	 *
	 * @return string
	 */
	protected function getPluginName() {
		if (empty($this->pluginName)) {
			$class = get_class($this);
			$startIndex = strpos($class, "\\");
			$endIndex = strpos($class, "\\", $startIndex + 1) - 1;

			return substr($class, $startIndex + 1, $endIndex - $startIndex);
		}

		return $this->pluginName;
	}

}
