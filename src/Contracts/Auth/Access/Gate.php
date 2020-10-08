<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth\Access;

interface Gate{
	
	/**
	 * 确定给定的能力是否已定义
	 *
	 * @param string $ability
	 * @return bool
	 */
	public function has($ability);
	
	/**
	 * 定义一个新的授权能力
	 *
	 * @param string          $ability
	 * @param callable|string $callback
	 * @return static
	 */
	public function define($ability, $callback);
	
	/**
	 * 定义一个授权策略能力
	 *
	 * @param string $class
	 * @param string $policy
	 * @return static
	 */
	public function policy($class, $policy);
	
	/**
	 * 在所有检查授权之前定义一个前置检查
	 *
	 * @param callable $callback
	 * @return static
	 */
	public function before(callable $callback);
	
	/**
	 * 在所有授权检查之后定义一个后置检查
	 *
	 * @param callable $callback
	 * @return static
	 */
	public function after(callable $callback);
	
	/**
	 * 检查当前用户是否未拥有给定授予的能力
	 *
	 * @param string      $ability
	 * @param array|mixed $arguments
	 * @return bool
	 */
	public function denies($ability, $arguments = []);
	
	/**
	 * 检查当前用户是否拥有给定授予的能力
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 * @return bool
	 */
	public function check($abilities, $arguments = []);
	
	/**
	 * 检查当前用户是否拥有其中一个授予的能力
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 * @return bool
	 */
	public function any($abilities, $arguments = []);
	
	/**
	 * 根据当前用户获取特定授权结果
	 *
	 * @param string      $ability
	 * @param array|mixed $arguments
	 * @return mixed
	 */
	public function raw($ability, $arguments = []);
	
	/**
	 * 获取给定类的策略实例
	 *
	 * @param object|string $class
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function getPolicyFor($class);
	
	/**
	 * 获取给定用户的保护实例
	 *
	 * @param mixed $user
	 * @return static
	 */
	public function forUser($user);
	
	/**
	 * 获取所有定义的授权能力
	 *
	 * @return array
	 */
	public function abilities();
}
