<?php

namespace Xin\Capsule;

use InvalidArgumentException;
use Xin\Support\Traits\Macroable;

abstract class Manager
{

	use WithConfig,
		WithContainer,
		Macroable {
		Macroable::__call as macroCall;
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
	public function __construct(array $config = [])
	{
		$this->config = array_merge_recursive($this->config, $config);
	}

	/**
	 * 获取一个驱动
	 * @param string|null $name
	 * @return mixed
	 */
	public function driver($name = null)
	{
		$name = $name ?: $this->getDefaultDriver();

		return $this->drivers[$name] ?? $this->drivers[$name] = $this->createDriver($name);
	}

	/**
	 * 解决给定的驱动
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected function createDriver($name)
	{
		// 获取驱动相关配置
		$config = $this->getDriverConfig($name);

		// 检查驱动配置是否存在，如果不存在则需要抛出异常，由上层调用者进行接管异常并处理
		if (is_null($config)) {
			throw new InvalidArgumentException(class_basename(get_class()) . " driver config [{$name}] is not defined.");
		}

		$instance = null;

		// 获取驱动名称，如果配置中不存在 driver 元素，则默认使用传递的 name
		$driver = $config['driver'] ?? $name;

		// 检查自定义驱动实例创建者是否存在，如果存在则使用驱动实例创建者创建实例
		// 否则则检查管理器是否存在创建驱动实例方法，如果存在则使用管理器提供的创建方法进行创建驱动实例
		if (isset($this->customCreators[$driver])) {
			$instance = $this->callCustomCreator($name, $driver, $config);
		} else {
			$createDriverMethod = 'create' . ucfirst($driver) . 'Driver';
			if (method_exists($this, $createDriverMethod)) {
				$instance = $this->{$createDriverMethod}($name, $config);
			} else {
				$createDefaultDriverMethod = 'createDefaultDriver';
				if (method_exists($this, $createDefaultDriverMethod)) {
					$instance = $this->{$createDefaultDriverMethod}($name, $config);
				}
			}
		}

		// 如果容器存在则返回，并且尝试给驱动实例设置容器实例
		if ($instance) {
			$this->setDriverContainer($instance);

			return $instance;
		}


		throw new InvalidArgumentException(
			class_basename(get_class()) . " driver [{$config['driver']}] for [{$name}] is not defined."
		);
	}

	/**
	 * 调用一个自定义的创建器
	 *
	 * @param string $name
	 * @param string $driver
	 * @param array $config
	 * @return mixed
	 */
	protected function callCustomCreator($name, $driver, array $config)
	{
		return $this->customCreators[$driver]($name, $config);
	}

	/**
	 * 给驱动设置容器实例
	 * @param string $driver
	 * @return void
	 */
	protected function setDriverContainer($driver)
	{
		if (method_exists($driver, 'setContainer')) {
			$driver->setContainer($this->getContainer());
		}
	}

	/**
	 * 自定义一个创建器
	 *
	 * @param string $driver
	 * @param \Closure $callback
	 * @return $this
	 */
	public function extend($driver, \Closure $callback)
	{
		$this->customCreators[$driver] = $callback;

		return $this;
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
	 * 获取所有已创建的驱动实例
	 * @return array
	 */
	public function getDrivers()
	{
		return $this->drivers;
	}

	/**
	 * 清除所有已解析的驱动实例
	 *
	 * @return $this
	 */
	public function forgetDrivers()
	{
		$this->drivers = [];

		return $this;
	}

	/**
	 * Pass dynamic methods call onto Flysystem.
	 *
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, array $parameters)
	{
		if (static::hasMacro($method)) {
			return $this->macroCall($method, $parameters);
		}

		return $this->driver()->{$method}(...array_values($parameters));
	}

}
