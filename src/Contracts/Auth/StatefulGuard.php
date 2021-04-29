<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

interface StatefulGuard extends Guard{

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
	 * 根据属性登录
	 *
	 * @param array         $credentials
	 * @param \Closure|null $notExistCallback
	 * @param \Closure|null $preCheckCallback
	 * @return \Xin\Contracts\Auth\Guard
	 */
	public function loginUsingCredential(
		array $credentials,
		\Closure $notExistCallback = null,
		\Closure $preCheckCallback = null
	);

	/**
	 * 退出登录
	 *
	 * @return void
	 */
	public function logout();
}
