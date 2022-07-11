<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Xin\Capsule\WithContainer;
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Contracts\Plugin\PluginInfo as PluginInfoContract;
use Xin\Support\Version;

class PluginInfo implements PluginInfoContract
{
	use WithContainer;

	/**
	 * @var PluginFactory
	 */
	protected $factory;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $pluginClass;

	/**
	 * @var array
	 */
	protected $info = null;

	/**
	 * @var array
	 */
	protected $configTemplate = null;

	/**
	 * PluginInfo constructor.
	 *
	 * @param string $name
	 * @param string $pluginClass
	 * @param \Xin\Contracts\Plugin\Factory $factory
	 */
	public function __construct($name, $pluginClass, PluginFactory $factory)
	{
		$this->name = $name;
		$this->pluginClass = $pluginClass;
		$this->factory = $factory;
	}

	/**
	 * @return array
	 */
	public function getInfo($name = null)
	{
		if (is_null($this->info)) {
			$path = $this->path() . 'manifest.php';
			$this->info = require_once $path;
		}

		return $name ? isset($this->info[$name]) ? $this->info[$name] : null : $this->info;
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function getVersion()
	{
		return $this->getInfo('version');
	}

	/**
	 * @inheritDoc
	 */
	public function checkVersion($newVersion)
	{
		return Version::check($this->getVersion(), $newVersion);
	}

	/**
	 * @inheritDoc
	 */
	public function path($path = '')
	{
		$rootPath = $this->factory->pluginPath($this->getName());

		return $rootPath . ($path ? $path . DIRECTORY_SEPARATOR : $path);
	}

	/**
	 * @inheritDoc
	 */
	public function plugin()
	{
		if (!is_object($this->pluginClass)) {
			$this->pluginClass = $this->makeClassInstance($this->pluginClass);
		}

		return $this->pluginClass;
	}

	/**
	 * @inheritDoc
	 */
	public function getConfigTemplate($config = [], $layer = null)
	{
		$template = $this->loadConfigTemplate($layer);

		foreach ($template as &$item) {
			foreach ($item['config'] as &$value) {
				$name = $value['name'];
				if (isset($config[$name])) {
					$value['value'] = $config[$name];
				} else {
					$value['value'] = value(isset($value['value']) ? $value['value'] : null);
				}
			}
			unset($value);
		}
		unset($item);

		return $template;
	}

	/**
	 * @inheritDoc
	 */
	public function getConfigTypeList()
	{
		$template = $this->loadConfigTemplate();

		$typeMap = [
			'switch' => 'int',
			'number' => 'int',
			'array' => 'array',
		];

		$result = [];
		foreach ($template as $item) {
			foreach ($item['config'] as $value) {
				if (isset($value['typeof'])) {
					$result[$value['name']] = $value['typeof'];
				} elseif (isset($typeMap[$value['type']])) {
					$result[$value['name']] = $typeMap[$value['type']];
				}
			}
		}

		return $result;
	}

	/**
	 * 解析值
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function resolveValue($value)
	{
		return $value instanceof \Closure ? $value() : $value;
	}

	/**
	 * 加载配置信息模板
	 *
	 * @return array
	 */
	protected function loadConfigTemplate($layer = null)
	{
		if (is_null($this->configTemplate)) {
			$configTemplatePath = $this->path() . "config.php";
			if (file_exists($configTemplatePath)) {
				$this->configTemplate = require_once $configTemplatePath;
			} else {
				$this->configTemplate = [];
			}
		}

		if ($layer) {
			$configTemplatePath = $this->path($layer) . "config.php";
			if (file_exists($configTemplatePath)) {
				$layerConfigTemplate = require_once $configTemplatePath;

				return array_merge_recursive($this->configTemplate, $layerConfigTemplate);
			}
		}

		return $this->configTemplate;
	}

}
