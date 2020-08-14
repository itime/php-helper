<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth\Access;

/**
 * Interface Gate
 */
interface Gate{

	/**
	 * 权限检查
	 *
	 * @param AuthenticRule $rule
	 * @return mixed
	 */
	public function checkAuth(AuthenticRule $rule);

	/**
	 * 是否是超级管理员
	 *
	 * @param mixed $uid
	 * @return bool
	 */
	public function checkAdministrator($uid);
}
