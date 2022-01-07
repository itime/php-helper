<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Support;

/**
 * Class Manager
 */
abstract class Manager {

	/**
	 * The application instance.
	 *
	 * @var \Psr\Container\ContainerInterface
	 */
	protected $app;

	/**
	 * The registered custom driver creators.
	 *
	 * @var array
	 */
	protected $customCreators = [];

	/**
	 * The array of created "drivers".
	 *
	 * @var array
	 */
	protected $drivers = [];

	/**
	 * Driver Suffix.
	 *
	 * @var string
	 */
	protected $driverSuffix = 'Driver';

	/**
	 * Create a new manager instance.
	 *
	 * @param \Psr\Container\ContainerInterface $app
	 * @return void
	 */
	public function __construct($app) {
		$this->app = $app;
	}

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	abstract public function getDefaultDriver();

	/**
	 * 获取驱动类型
	 *
	 * @param string $name
	 * @return mixed
	 */
	abstract protected function resolveType($name);

	/**
	 * 获取驱动配置
	 *
	 * @param string $name
	 * @return mixed
	 */
	abstract protected function resolveConfig($name);

	/**
	 * Get a driver instance.
	 *
	 * @param string|null $name
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function driver($name = null) {
		$name = $name ?: $this->getDefaultDriver();

		if (is_null($name)) {
			throw new \InvalidArgumentException(sprintf(
				'Unable to resolve NULL driver for [%s].', static::class
			));
		}

		return $this->drivers[$name] = $this->getDriver($name);
	}

	/**
	 * 获取驱动实例
	 *
	 * @param string $name
	 * @return mixed
	 */
	protected function getDriver($name) {
		return $this->drivers[$name] ?? $this->createDriver($name);
	}

	/**
	 * 获取驱动参数
	 *
	 * @param $name
	 * @return array
	 */
	protected function resolveParams($name) {
		$config = $this->resolveConfig($name);

		return [$config];
	}

	/**
	 * Create a new driver instance.
	 *
	 * @param string $name
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	protected function createDriver($name) {
		$type = $this->resolveType($name);
		$params = $this->resolveParams($name);

		// First, we will determine if a custom driver creator exists for the given driver and
		// if it does not we will check for a creator method for the driver. Custom creator
		// callbacks allow developers to build their own "drivers" easily using Closures.
		if (isset($this->customCreators[$type])) {
			return $this->callCustomCreator($type, $params);
		} else {
			$method = 'create' . Str::studly($type) . $this->driverSuffix;

			if (method_exists($this, $method)) {
				return call_user_func_array([$this, $method], $params);
			}
		}

		throw new \InvalidArgumentException("Driver [$type] not supported.");
	}

	/**
	 * Call a custom driver creator.
	 *
	 * @param string $name
	 * @param array  $params
	 * @return mixed
	 */
	protected function callCustomCreator($name, $params) {
		return call_user_func_array(
			$this->customCreators[$name],
			$params
		);
	}

	/**
	 * Register a custom driver creator Closure.
	 *
	 * @param string   $driver
	 * @param \Closure $callback
	 * @return $this
	 */
	public function extend($driver, \Closure $callback) {
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	/**
	 * Get all of the created "drivers".
	 *
	 * @return array
	 */
	public function getDrivers() {
		return $this->drivers;
	}

	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {
		return $this->driver()->$method(...$parameters);
	}

}
