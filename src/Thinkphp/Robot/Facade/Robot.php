<?php

namespace Xin\Thinkphp\Robot\Facade;

use think\App;
use Xin\Contracts\Robot\Robot as RobotContract;

/**
 * @mixin RobotContract
 */
class Robot
{
	/**
	 * @var RobotContract
	 */
	protected $robot;

	/**
	 * @param RobotContract|null $robot
	 */
	protected function __construct(RobotContract $robot = null)
	{
		$this->robot = $robot;
	}

	/**
	 * @inerhitDoc
	 */
	public function __call($name, $arguments)
	{
		if (!$this->robot) {
			return null;
		}

		return call_user_func_array([$this->robot, $name], $arguments);
	}

	/**
	 * @inerhitDoc
	 */
	public static function __callStatic($name, $arguments)
	{
		return call_user_func_array([static::via(), $name], $arguments);
	}

	/**
	 * 选用机器人
	 * @param string $name
	 * @return static
	 */
	public static function via($name = null)
	{
		$robot = null;

		if (App::getInstance()->has('robot')) {
			/** @var RobotContract $robot */
			$robot = App::getInstance()->make('robot')->robot($name);
		}

		return new static($robot);
	}

	/**
	 * 选用默认类型机器人
	 * @return static
	 */
	public static function viaDefault()
	{
		return static::via();
	}

	/**
	 * 选用告警类型机器人
	 * @return static
	 */
	public static function viaDanger()
	{
		return static::via('danger');
	}

	/**
	 * 选用通知类型机器人
	 * @return static
	 */
	public static function viaInfo()
	{
		return static::via('info');
	}
}