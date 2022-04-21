<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Repository;

use Xin\Contracts\Repository\Repository;
use Xin\Middleware\MiddlewareManager;
use Xin\Support\Arr;

abstract class AbstractRepository implements Repository
{
	use HasMiddleware;

	/**
	 * @var array
	 */
	protected $options = [
		'find_or_fail' => true,
		'allow_force_delete' => false
	];

	/**
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		$this->options = array_replace_recursive($this->options, $options);
		$this->middlewareManager = new MiddlewareManager();

		if (isset($options['handler'])) {
			$this->setupHandler($options['handler']);
		}

		$this->registerSearchMiddleware();
	}

	/**
	 * 注册搜索中间件
	 * @return void
	 */
	abstract protected function registerSearchMiddleware();

	/**
	 * 执行事务
	 * @param callable $callback
	 * @return mixed
	 */
	abstract protected function transaction(callable $callback);

	/**
	 * Get query instance
	 * @param mixed $filter
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	abstract protected function query($filter, array $with, array $options);

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	/**
	 * 获取配置项
	 * @param string $key
	 * @param mixed $default
	 * @return array|\ArrayAccess|mixed
	 */
	protected function getOption($key, $default = null)
	{
		return Arr::get($this->options, $key, $default);
	}

	/**
	 * 设置配置项
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setOption($key, $value)
	{
		Arr::set($this->options, $key, $value);
	}

	/**
	 * 获取搜索字段列表
	 * @return array|\ArrayAccess|mixed
	 */
	public function getSearchFields()
	{
		return $this->getOption('search_fields', []);
	}

	/**
	 * 设置搜索字段列表
	 * @param array $fields
	 * @return void
	 */
	public function setSearchFields(array $fields)
	{
		$this->setOption('search_fields', $fields);
	}

	/**
	 * @param string $modelClass
	 * @param array $options
	 * @return static
	 */
	public static function ofModel($modelClass, $options = [])
	{
		return new static(array_replace_recursive([
			'model' => $modelClass,
		], $options));
	}
}