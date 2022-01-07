<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

/**
 * Interface UserProvider
 */
interface UserProvider {

	/**
	 * 根据唯一ID取用户
	 *
	 * @param mixed $identifier
	 * @return mixed
	 */
	public function getById($identifier);

	/**
	 * 根据凭证信息获取用户
	 *
	 * @param array|\Closure $credentials
	 * @return mixed
	 */
	public function getByCredentials($credentials);

	/**
	 * 验证密码是否正确
	 *
	 * @param mixed  $user
	 * @param string $password
	 * @return boolean
	 */
	public function validatePassword($user, $password);

	/**
	 * 获取密码字段名称
	 *
	 * @return string
	 */
	public function getPasswordName();

}
