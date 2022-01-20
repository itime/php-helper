<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Support\Arr;
use Xin\Support\Collection;

class PluginManager implements PluginFactory {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var Collection
	 */
	protected $plugins;

	/**
	 * @var array
	 */
	protected $filters = [];

	/**
	 * @var array
	 */
	protected $isPluginBoot = false;

	/**
	 * AbstractPluginManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function rootPath($path = '') {
		return $this->config['path'] . ($path ? $path . DIRECTORY_SEPARATOR : $path);
	}

	/**
	 * @inheritDoc
	 */
	public function has($plugin) {
		return class_exists($this->pluginClass($plugin, "Plugin"));
	}

	/**
	 * @inheritDoc
	 */
	public function installPlugin($plugin) {
		$pluginInfo = $this->plugin($plugin);

		$this->plugin($plugin)->plugin()->install($pluginInfo, $this);

		return $pluginInfo;
	}

	/**
	 * @inheritDoc
	 */
	public function uninstallPlugin($plugin) {
		$pluginInfo = $this->plugin($plugin);

		$this->plugin($plugin)->plugin()->uninstall($pluginInfo, $this);

		return $pluginInfo;
	}

	/**
	 * @inheritDoc
	 */
	public function plugin($plugin) {
		if ($this->plugins->has($plugin)) {
			return $this->plugins->get($plugin);
		}

		$class = $this->pluginClass($plugin, "Plugin");
		if (!class_exists($class)) {
			throw new PluginNotFoundException($plugin);
		}

		$pluginInfo = new PluginInfo($plugin, $class, $this);
		$this->plugins->set($plugin, $pluginInfo);

		return $pluginInfo;
	}

	/**
	 * @inheritDoc
	 */
	public function plugins() {
		if ($this->plugins) {
			return $this->useFilter();
		}

		$plugins = [];
		$fileIterator = new \FilesystemIterator($this->rootPath());
		foreach ($fileIterator as $file) {
			if (!$file->isDir()) {
				continue;
			}

			$name = $file->getFilename();
			$class = $this->pluginClass($name, "Plugin");
			if (!class_exists($class)) {
				continue;
			}

			$plugins[$name] = new PluginInfo($name, $class, $this);
		}

		$this->plugins = new Collection($plugins);

		return $this->useFilter();
	}

	/**
	 * 使用过滤器返回插件列表
	 * @return Collection
	 */
	protected function useFilter() {
		return array_reduce($this->filters, function (Collection $plugins, $filter) {
			return $plugins->filter($filter);
		}, $this->plugins);
	}

	/**
	 * @inheritDoc
	 */
	public function filter(callable $filterCallback) {
		$this->filters[] = $filterCallback;
	}

	/**
	 * @inheritDoc
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function pluginBoot(array $plugins = []) {
		if ($this->isPluginBoot) {
			return;
		}

		$this->isPluginBoot = true;

		foreach ($this->plugins as $plugin => $pluginInfo) {
			$this->plugin($plugin)->plugin()->boot($pluginInfo, $this);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function pluginClass($plugin, $class) {
		return "\\" . $this->rootNamespace() . "\\{$plugin}\\{$class}";
	}

	/**
	 * @inheritDoc
	 */
	public function controllerClass($plugin, $controller, $layer = 'controller') {
		return $this->pluginClass($plugin, "{$layer}\\{$controller}Controller");
	}

	/**
	 * @inheritDoc
	 */
	public function pluginPath($plugin) {
		return $this->rootPath($plugin);
	}

	/**
	 * 默认命名空间
	 *
	 * @return string
	 */
	public function rootNamespace() {
		return Arr::get($this->config, 'namespace', 'plugin');
	}

	/**
	 * @inheritDoc
	 */
	public function config($name, $default = null) {
		return Arr::get($this->config, $name, $default);
	}

}
