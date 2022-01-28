<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

interface UserWechatBinder
{

	/**
	 * get user bind model by openid.
	 *
	 * @param string $openid
	 * @return mixed
	 */
	public function getByOpenId($openid);

	/**
	 * bind model to user.
	 *
	 * @param int $userId
	 * @param string $openId
	 * @return mixed
	 */
	public function bindToUser($userId, $openId);

}
