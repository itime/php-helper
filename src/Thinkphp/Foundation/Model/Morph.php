<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Model;

use think\db\exception\ModelNotFoundException;
use think\Model;
use Xin\Foundation\ClassMapManager;

/**
 * Class Morph
 * @mixin ClassMapManager
 * @see ClassMapManager
 */
final class Morph
{
	/**
	 * @var \Xin\Foundation\ClassMapManager
	 */
	protected static $classMapManager;

	/**
	 * 私有构造器
	 */
	private function __construct()
	{
	}

	/**
	 * @return \Xin\Foundation\ClassMapManager
	 */
	public static function getClassMapManager()
	{
		if (self::$classMapManager === null) {
			self::$classMapManager = new ClassMapManager();
		}

		return self::$classMapManager;
	}

	/**
	 * 获取类型列表
	 *
	 * @return array
	 */
	public static function getTypeList()
	{
		return self::getClassMapManager()->getMaps();
	}

	/**
	 * 绑定多态关联类型
	 *
	 * @param string $type
	 * @param string $modelClass
	 */
	public static function bindType($type, $modelClass)
	{
		if (self::hasType($type)) {
			throw new \LogicException("morph type {$type} duplicate defined.");
		}

		self::getClassMapManager()->bind($type, $modelClass);
	}

	/**
	 * 判断多态类型是否存在
	 *
	 * @param string $type
	 * @return bool
	 */
	public static function hasType($type)
	{
		return self::getClassMapManager()->has($type);
	}

	/**
	 * 获取多态类型指定的类
	 *
	 * @param string $type
	 * @return string
	 */
	public static function getType($type)
	{
		if (!self::hasType($type)) {
			throw new \LogicException("morph type {$type} not defined.");
		}

		return self::getClassMapManager()->get($type);
	}

	/**
	 * 检查对应关联的资源是否存在
	 *
	 * @param string $type
	 * @param int $id
	 * @return Model
	 * @throws ModelNotFoundException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 */
	public static function checkExist($type, $id)
	{
		$class = self::getType($type);

		$result = null;
		if (method_exists($class, 'checkMorphExist')) {
			$result = call_user_func([$class, 'checkMorphExist'], $id);
		} elseif (is_subclass_of($class, \think\Model::class)) {
			$result = (new $class)->where('id', $id)->failException()->find();
		}

		if (!$result) {
			throw new ModelNotFoundException("morph resource not found!", $class);
		}

		return $result;
	}

	/**
	 * 调用对应关联资源的方法
	 *
	 * @param string $type
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 * @deprecated
	 */
	public static function callMethod($type, $method, $args = [])
	{
		return self::callStaticMethod($type, $method, $args);
	}

	/**
	 * 调用对应关联资源的方法
	 *
	 * @param string $type
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function callStaticMethod($type, $method, $args = [])
	{
		$class = self::getType($type);

		if (!method_exists($class, $method)) {
			return null;
		}

		return call_user_func_array([$class, $method], $args);
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		return call_user_func_array([self::getClassMapManager(), $name], $arguments);
	}

}
