<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Support;

class Reflect
{

	public const VISIBLE_PUBLIC = 0;

	public const VISIBLE_PROTECTED = 1;

	public const VISIBLE_PRIVATE = 2;

	/**
	 * 获取类方法可见范围
	 *
	 * @param mixed $class
	 * @param string $method
	 * @return int
	 * @throws \ReflectionException
	 */
	public static function getMethodVisible($class, $method)
	{
		$ref = new \ReflectionMethod($class, $method);
		if ($ref->isPublic()) {
			return self::VISIBLE_PUBLIC;
		}

		if ($ref->isProtected()) {
			return self::VISIBLE_PROTECTED;
		}

		return self::VISIBLE_PRIVATE;
	}

	/**
	 * 方法可见范围
	 *
	 * @param mixed $class
	 * @param string $method
	 * @return int
	 */
	public static function methodVisible($class, $method)
	{
		try {
			return self::getMethodVisible($class, $method);
		} catch (\ReflectionException $e) {
		}

		return self::VISIBLE_PRIVATE;
	}

	/**
	 * 回退试调用类方法
	 *
	 * @param mixed $class
	 * @param array $methods
	 * @param array $args
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public static function fallbackCalls($class, $methods, $args = [])
	{
		foreach ($methods as $method) {
			if (self::VISIBLE_PUBLIC === self::getMethodVisible($class, $method)) {
				return call_user_func_array([$class, $method], $args);
			}
		}

		return null;
	}

	/**
	 * 获取类属性
	 * @param string|object $class
	 * @param string $property
	 * @return \ReflectionProperty
	 * @throws \ReflectionException
	 */
	public static function getProperty($class, $property)
	{
		$property = new \ReflectionProperty($class, $property);
		$property->setAccessible(true);

		return $property;
	}

	/**
	 * 获取属性值
	 * @param object $classInstance
	 * @param string $property
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public static function getPropertyValue($classInstance, $property)
	{
		return static::getProperty($classInstance, $property)->getValue($classInstance);
	}

	/**
	 * 获取属性值
	 * @param object $classInstance
	 * @param string $property
	 * @return mixed
	 * @return mixed
	 */
	public static function propertyValue($classInstance, $property)
	{
		try {
			return static::getPropertyValue($classInstance, $property);
		} catch (\ReflectionException $e) {
			return null;
		}
	}

	/**
	 * @param object $classInstance
	 * @param string $property
	 * @param mixed $value
	 * @return \ReflectionProperty
	 * @throws \ReflectionException
	 */
	public static function setPropertyValue($classInstance, $property, $value)
	{
		$property = static::getProperty($classInstance, $property);
		$property->setValue($classInstance, $value);

		return $property;
	}

}
