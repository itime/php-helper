<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas\Wechat;

interface ConfigProvider {

	/**
	 * 根据 id 取出配置
	 * @param int    $id
	 * @param string $type
	 * @return array
	 */
	public function retrieveById($id, $type);

	/**
	 * 根据 appId 取出配置
	 * @param int    $appId
	 * @param string $type
	 * @param string $name
	 * @return array
	 */
	public function retrieveByAppId($appId, $type, $name = null);

}
