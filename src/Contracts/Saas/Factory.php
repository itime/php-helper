<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas;

interface Factory {

	/**
	 * 获取应用信息
	 *
	 * @param string|null $field
	 * @param mixed       $default
	 * @return mixed
	 */
	public function getAppInfo($field = null, $default = null);

	/**
	 * 获取应用id
	 *
	 * @return int
	 */
	public function getAppId();

	/**
	 * 暂存应用信息
	 *
	 * @param mixed $info
	 * @return bool
	 */
	public function temporaryAppInfo($info);

	/**
	 * 获取config实例
	 *
	 * @return \Xin\Contracts\Config\Repository
	 */
	public function getConfig();

}
