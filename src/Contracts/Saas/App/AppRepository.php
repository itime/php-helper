<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas\App;

interface AppRepository
{

	/**
	 * 根据AccessId获取应用信息
	 *
	 * @param string $accessId
	 * @return mixed
	 */
	public function retrieveByAccessId($accessId);

}
