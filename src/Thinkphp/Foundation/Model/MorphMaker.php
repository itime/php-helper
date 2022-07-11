<?php

namespace Xin\Thinkphp\Foundation\Model;

use Closure;
use think\Model;

class MorphMaker
{
	/**
	 * @var string
	 */
	protected $targetClass;

	/**
	 * @var bool
	 */
	protected $simpleScope = true;

	/**
	 * @var bool
	 */
	protected $withTrashed = true;

	/**
	 * @param string $targetClass
	 */
	protected function __construct($targetClass)
	{
		$this->targetClass = $targetClass;
	}

	/**
	 * @param Model $model
	 * @return void
	 * @throws \ReflectionException
	 */
	public function handle(Model $model)
	{
		if (get_class($model) === $this->targetClass) {
			return;
		}

		$reflectClass = new \ReflectionClass($model);

		//设置多态模型包含软删条件
		if ($this->hasMorphModelWithTrashed($model, $reflectClass)) {
			$this->setMorphModelWithTrashed($model, $reflectClass);
		}

		// 设置获取简单作用域
		if ($this->isSimpleScope() && $this->hasMorphModelSimpleScope($model)) {
			$this->setMorphModelSimpleScope($model, $reflectClass);
		}
	}

	/**
	 * 判断是否需要使用简单获取数据作用域
	 * @param Model $model
	 * @return bool
	 */
	protected function hasMorphModelSimpleScope(Model $model)
	{
		return method_exists($model, 'scopeSimple') || $this->hasMorphModelPlainListScope($model);
	}

	/**
	 * 判断是否需要使用简单获取数据作用域（旧模式）
	 * @param Model $model
	 * @return bool
	 */
	protected function hasMorphModelPlainListScope(Model $model)
	{
		return method_exists($model, 'scopePlainList');
	}

	/**
	 * 设置多态模型使用简单获取数据作用域
	 * @param Model $model
	 * @param \ReflectionClass $reflectClass
	 * @return void
	 * @throws \ReflectionException
	 */
	protected function setMorphModelSimpleScope(Model $model, \ReflectionClass $reflectClass)
	{
		$property = $reflectClass->getProperty('globalScope');
		$property->setAccessible(true);
		$value = $property->getValue($model);

		if ($this->hasMorphModelPlainListScope($model)) {
			$value[] = 'plainList';
		} else {
			$value[] = 'simple';
		}

		$property->setValue($model, $value);
	}

	/**
	 * 是否需要包含软删数据
	 * @param Model $model
	 * @param \ReflectionClass $reflectClass
	 * @return bool
	 */
	protected function hasMorphModelWithTrashed(Model $model, \ReflectionClass $reflectClass)
	{
		return $this->isWithTrashed() && $reflectClass->hasProperty('withTrashed');
	}

	/**
	 * 设置多态模型包含软删条件
	 * @param Model $model
	 * @param \ReflectionClass $reflectClass
	 * @return void
	 * @throws \ReflectionException
	 */
	protected function setMorphModelWithTrashed(Model $model, \ReflectionClass $reflectClass)
	{
		$property = $reflectClass->getProperty('withTrashed');
		$property->setAccessible(true);
		$property->setValue($model, true);
	}

	/**
	 * @return bool
	 */
	public function isSimpleScope()
	{
		return $this->simpleScope;
	}

	/**
	 * @param bool $simpleScope
	 * @return $this
	 */
	public function setSimpleScope($simpleScope)
	{
		$this->simpleScope = $simpleScope;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isWithTrashed()
	{
		return $this->withTrashed;
	}

	/**
	 * @param bool $withTrashed
	 * @return $this
	 */
	public function setWithTrashed($withTrashed)
	{
		$this->withTrashed = $withTrashed;

		return $this;
	}

	/**
	 * 生成 Closure
	 * @param string $targetClass
	 * @param callable $callback
	 * @return Closure
	 */
	public static function newClosure($targetClass, $callback = null)
	{
		$maker = new static($targetClass);
		$callback && $callback($maker);

		return Closure::fromCallable([$maker, 'handle']);
	}

	/**
	 * 注入到模型中
	 * @param string $targetClass
	 * @param callable $callback
	 * @return void
	 */
	public static function maker($targetClass, $callback = null)
	{
		$closure = static::newClosure($targetClass, $callback);

		Model::maker($closure);
	}
}