<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Wechat;

interface Factory {

	/**
	 * 获取默认微信开放平台实例
	 *
	 * @param string $name
	 * @param array  $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatform($name = null, array $options = []);

	/**
	 * 开放平台配置是否存在
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasOpenPlatform($name = null);

	/**
	 * 获取默认微信公众号实例
	 *
	 * @param string $name
	 * @param array  $options
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public function official($name = null, array $options = []);

	/**
	 * 微信公众号配置是否存在
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasOfficial($name = null);

	/**
	 * 获取默认小程序实例
	 *
	 * @param string $name
	 * @param array  $options
	 * @return \EasyWeChat\MiniProgram\Application
	 */
	public function miniProgram($name = null, array $options = []);

	/**
	 * 小程序配置是否存在
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasMiniProgram($name = null);

	/**
	 * 获取企业微信实例
	 * @param string $name
	 * @return \EasyWeChat\Work\Application
	 */
	public function work($name = null, array $options = []);

	/**
	 * 企业微信配置是否存在
	 * @param string $name
	 * @return boolean
	 */
	public function hasWork($name = null);

	/**
	 * 获取企业微信开放平台实例
	 * @param string $name
	 * @return \EasyWeChat\OpenWork\Application
	 */
	public function openWork($name = null, array $options = []);

	/**
	 * 企业微信开放平台配置是否存在
	 * @return boolean
	 */
	public function hasOpenWork($name = null);

	/**
	 * 获取配置数据
	 *
	 * @param string $key
	 * @return array
	 */
	public function getConfig($key = null, $default = null);

}
