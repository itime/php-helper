<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 */

namespace Xin\Contracts\Auth;

interface Guard
{

	/**
	 * 获取用户信息
	 *
	 * @param string|null $field
	 * @param mixed|null $default
	 * @param int $verifyType
	 * @return mixed
	 */
	public function getUser($field = null, $default = null, $verifyType = AuthVerifyType::BASE);

	/**
	 * 获取用户id
	 *
	 * @param int $verifyType
	 * @return int
	 */
	public function getUserId($verifyType = AuthVerifyType::BASE);

	/**
	 * 暂存用户信息
	 *
	 * @param mixed $user
	 * @return mixed
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function temporaryUser($user);

	/**
	 * 检查是否已登录
	 *
	 * @return bool
	 */
	public function check();

	/**
	 * 检查当前是否是游客模式
	 *
	 * @return bool
	 */
	public function guest();

	/**
	 * 设置获取用户信息预检查回调
	 *
	 * @param \Closure $preCheckCallback
	 */
	public function setPreCheckCallback(\Closure $preCheckCallback);

}
