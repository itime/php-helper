<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas;

interface AppGuard{
	
	/**
	 * 获取应用信息
	 *
	 * @param string|null $field
	 * @param mixed       $default
	 * @param bool        $abort
	 * @return mixed
	 */
	public function getAppInfo($field = null, $default = null, $abort = true);
	
	/**
	 * 获取应用id
	 *
	 * @param bool $abort
	 * @return int
	 */
	public function getAppId($abort = true);
	
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
	 * @return \Xin\Contracts\Foundation\ConfigRepository
	 */
	public function getConfig();
}
