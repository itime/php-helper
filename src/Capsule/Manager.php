<?php

namespace Xin\Capsule;

use InvalidArgumentException;
use Xin\Support\Traits\Macroable;

abstract class Manager {

	use WithConfig,
		WithContainer,
		Macroable {
		__call as macroCall;
	}

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var array
	 */
	protected $drivers = [];

	/**
	 * The registered custom driver creators.
	 *
	 * @var array
	 */
	protected $customCreators = [];

	/**
	 * Create a new Manager instance.
	 * @param array $config
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function __construct(array $config = []) {
		$this->config = array_merge_recursive($this->config, $config);
	}

	/**
	 * 获取一个驱动
	 * @param string|null $name
	 * @return mixed
	 */
	public function driver(string $name = null) {
		$name = $name ?: $this->getDefaultDriver();

		return $this->drivers[$name] ?? $this->drivers[$name] = $this->resolve($name);
	}

	/**
	 * 解决给定的驱动
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected function resolve(string $name) {
		$config = $this->getDriverConfig($name);

		if (is_null($config)) {
			throw new InvalidArgumentException(class_basename(get_class()) . " config [{$name}] is not defined.");
		}

		if (isset($this->customCreators[$config['driver']])) {
			return $this->callCustomCreator($name, $config);
		}

		$driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

		if (method_exists($this, $driverMethod)) {
			return $this->{$driverMethod}($name, $config);
		}

		throw new InvalidArgumentException(
			class_basename(get_class()) . " driver [{$config['driver']}] for [{$name}] is not defined."
		);
	}

	/**
	 * 调用一个自定义的创建器
	 *
	 * @param string $name
	 * @param array  $config
	 * @return mixed
	 */
	protected function callCustomCreator($name, array $config) {
		return $this->customCreators[$config['driver']]($name, $config);
	}

	/**
	 * 获取默认驱动
	 * @return string
	 */
	abstract protected function getDefaultDriver();


	/**
	 * 设置默认驱动
	 * @param string $name
	 */
	abstract protected function setDefaultDriver($name);

	/**
	 * 获取驱动配置
	 * @param string $name
	 * @return array|\ArrayAccess|mixed
	 */
	abstract public function getDriverConfig($name);

	/**
	 * Pass dynamic methods call onto Flysystem.
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, array $parameters) {
		if (static::hasMacro($method)) {
			return $this->macroCall($method, $parameters);
		}

		return $this->driver()->{$method}(...array_values($parameters));
	}

}
