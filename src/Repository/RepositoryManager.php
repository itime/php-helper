<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Repository;

use RuntimeException;
use Xin\Contracts\Repository\Factory;
use Xin\Support\Arr;
use Xin\Support\Str;
use Xin\Thinkphp\Repository\Repository;

class RepositoryManager implements Factory
{
	/**
	 * @var array
	 */
	protected $instance = [];

	/**
	 * @var array
	 */
	protected $bind = [];

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var callable
	 */
	protected $classResolver;

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @inerhitDoc
	 */
	public function repository($name)
	{
		if (!isset($this->instance[$name])) {
			$this->instance[$name] = $this->createRepository($name);
		}

		return $this->instance[$name];
	}

	/**
	 * 创建仓库实例
	 * @param string|object $name
	 * @return \Xin\Contracts\Repository\Repository
	 */
	protected function createRepository($name)
	{
		if (isset($this->bind[$name])) {
			$resolver = $this->bind[$name];
			if (is_callable($resolver)) {
				$instance = $resolver();
			} else {
				return $this->createRepository($resolver);
			}
		} elseif (class_exists($name) || is_object($name)) {
			$instance = $this->createRepositoryFor($name);
		} else {
			$instance = $this->discover($name);
		}

		return $instance;
	}

	/**
	 * 为提供的class或object创建仓库
	 * @param string|object $class
	 * @return \Xin\Contracts\Repository\Repository
	 */
	protected function createRepositoryFor($class)
	{
		if (method_exists($class, 'getRepository')) {
			return call_user_func([$class, 'getRepository']);
		}

		if (method_exists($class, 'getMakeRepositoryConfig')) {
			$config = call_user_func([$class, 'getMakeRepositoryConfig']);

			return new Repository($config);
		}

		throw new RuntimeException("[{$class}] cannot create repository because getRepository or getMakeRepositoryConfig is not defined.");
	}

	/**
	 * 尝试发现仓库，并生成实例
	 * @param string $name
	 * @return \Xin\Contracts\Repository\Repository
	 */
	protected function discover($name)
	{
		$class = $this->discoverRepositoryName($name);
		if ($class) {
			return $this->resolveClass($class);
		}

		throw new RuntimeException("[{$name}] not defined.");
	}

	/**
	 * 尝试发现仓库名字
	 * @param $name
	 * @return string|null
	 */
	protected function discoverRepositoryName($name)
	{
		$repositoryClass = Str::studly($name) . 'Repository';

		if ($this->getDiscoverResolver()) {
			$instance = call_user_func(
				$this->getDiscoverResolver(), $name
			);

			if ($instance) {
				return $instance;
			}
		}

		foreach ($this->getDiscoverNamespaces() as $namespace) {
			$class = $namespace . $repositoryClass;
			if (class_exists($class)) {
				return $class;
			}
		}

		return null;
	}

	/**
	 * 获取仓库发现命名空间
	 * @return array|\ArrayAccess|mixed
	 */
	protected function getDiscoverNamespaces()
	{
		return Arr::get($this->config, 'discover.namespaces', []);
	}

	/**
	 * 获取仓库发现解析器
	 * @return array|\ArrayAccess|mixed
	 */
	protected function getDiscoverResolver()
	{
		return Arr::get($this->config, 'discover.resolver');
	}

	/**
	 * @inerhitDoc
	 */
	public function register($name, $concrete)
	{
		if (is_callable($concrete)) {
			$this->bind[$name] = $concrete;
		} else {
			$this->instance[$name] = $concrete;
		}
	}

	/**
	 * @inerhitDoc
	 */
	public function forget($name, $unbind = false)
	{
		unset($this->instance[$name]);

		if ($unbind) {
			unset($this->bind[$name]);
		}
	}

	/**
	 * @return callable
	 */
	public function getClassResolver()
	{
		return $this->classResolver;
	}

	/**
	 * @param callable $classResolver
	 */
	public function setClassResolver(callable $classResolver)
	{
		$this->classResolver = $classResolver;
	}

	/**
	 * 解析类
	 * @param string $class
	 * @return \Xin\Contracts\Repository\Repository
	 */
	protected function resolveClass($class)
	{
		if (!$this->classResolver) {
			throw new \RuntimeException("class resolver not defined.");
		}

		return call_user_func($this->classResolver, $class);
	}

}
