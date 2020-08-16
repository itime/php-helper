<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 */

namespace Xin\Contracts\Auth;

/**
 * Interface Guard
 */
interface Guard{
	
	/**
	 * 获取用户信息
	 *
	 * @param string $field
	 * @param mixed  $default
	 * @param bool   $abort
	 * @return mixed
	 */
	public function getUserInfo($field = null, $default = null, $abort = true);
	
	/**
	 * 获取用户id
	 *
	 * @param bool $abort
	 * @return int
	 */
	public function getUserId($abort = true);
	
	/**
	 * 获取用户密码
	 *
	 * @param bool $abort
	 * @return string|false
	 */
	public function getUserPassword($abort = true);
	
	/**
	 * 更新用户信息-持久化数据
	 * @param array $data
	 * @param bool  $abort
	 * @return mixed
	 */
	public function saveUserInfo(array $data, $abort = true);
	
	/**
	 * 暂存用户信息
	 *
	 * @param mixed $user
	 * @return mixed
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function temporaryUser($user);
	
	/**
	 * 登录
	 *
	 * @param mixed $user
	 * @return \Xin\Contracts\Auth\Guard
	 */
	public function login($user);
	
	/**
	 * 根据ID登录
	 *
	 * @param mixed $id
	 * @return \Xin\Contracts\Auth\Guard
	 */
	public function loginUsingId($id);
	
	/**
	 * 根据字段来登录 并且验证密码
	 *
	 * @param mixed  $field
	 * @param string $credential
	 * @param string $password
	 * @return mixed
	 */
	public function loginUsingPassword($field, $credential, $password);
	
	/**
	 * 根据属性登录
	 *
	 * @param string   $field
	 * @param string   $credential
	 * @param \Closure $notExistCallback
	 * @return \Xin\Contracts\Auth\Guard
	 */
	public function loginUsingCredential($field, $credential, \Closure $notExistCallback = null);
	
	/**
	 * 退出登录
	 *
	 * @return void
	 */
	public function logout();
	
}
