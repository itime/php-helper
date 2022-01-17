<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Model;

/**
 * Class Morph
 * @deprecated
 * @see \Xin\Foundation\ClassMapManager
 */
class Morph {

	/**
	 * 多态关联类型
	 *
	 * @var array
	 */
	protected static $morphList = [];

	/**
	 * 获取类型列表
	 *
	 * @return array
	 */
	public static function getTypeList() {
		return self::$morphList;
	}

	/**
	 * 绑定多态关联类型
	 *
	 * @param string $type
	 * @param string $modelClass
	 */
	public static function bindType($type, $modelClass) {
		if (static::hasType($type)) {
			throw new \LogicException("morph type {$type} duplicate defined.");
		}

		self::$morphList[$type] = $modelClass;
	}

	/**
	 * 判断多态类型是否存在
	 *
	 * @param string $type
	 * @return bool
	 */
	public static function hasType($type) {
		return isset(self::$morphList[$type]);
	}

	/**
	 * 获取多态类型指定的类
	 *
	 * @param string $type
	 * @return string
	 */
	public static function getType($type) {
		if (!static::hasType($type)) {
			throw new \LogicException("morph type {$type} not defined.");
		}

		return self::$morphList[$type];
	}

	/**
	 * 检查对应关联的资源是否存在
	 *
	 * @param string $type
	 * @param int    $id
	 * @return bool
	 */
	public static function checkExist($type, $id) {
		$class = static::getType($type);

		$result = false;
		if (method_exists($class, 'checkMorphExist')) {
			$result = call_user_func([$class, 'checkMorphExist'], $id);
		} elseif (is_subclass_of($class, \think\Model::class)) {
			$result = $class::where('id', $id)->failException()->value('id');
		}

		if (!$result) {
			throw new \LogicException("morph resource not found!");
		}

		return true;
	}

	/**
	 * 调用对应关联资源的方法
	 *
	 * @param string $type
	 * @param string $method
	 * @param array  $args
	 * @return false|mixed
	 */
	public static function callMethod($type, $method, $args = []) {
		$class = static::getType($type);

		if (!method_exists($class, $method)) {
			return null;
		}

		return call_user_func_array([$class, $method], $args);
	}

}
