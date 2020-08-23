<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

interface UserWechatBinder{
	
	/**
	 * get user bind model by openid.
	 *
	 * @param string $openid
	 * @return mixed
	 */
	public function getByOpenId($openid);
	
	/**
	 * get user bind model by user'id and app'id.
	 *
	 * @param string $userId
	 * @param string $appId
	 * @return mixed
	 */
	public function getByUserIdAndAppId($userId, $appId);
	
	/**
	 * bind model to user.
	 *
	 * @param string $openId
	 * @param int    $userId
	 * @return mixed
	 */
	public function bindToUser($openId, $userId);
}
