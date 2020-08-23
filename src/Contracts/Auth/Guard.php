<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 */

namespace Xin\Contracts\Auth;

interface Guard{
	
	/**
	 * 获取用户信息
	 *
	 * @param string $field
	 * @param mixed  $default
	 * @param bool   $abort
	 * @return mixed
	 */
	public function getUser($field = null, $default = null, $abort = true);
	
	/**
	 * 获取用户id
	 *
	 * @param bool $abort
	 * @return int
	 */
	public function getUserId($abort = true);
	
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
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest();
	
}
