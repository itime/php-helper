<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

/**
 * @deprecated
 */
interface Wechat {

	/**
	 * 获取默认微信开放平台实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatform(array $options = []);

	/**
	 * 开放平台配置是否存在
	 *
	 * @return bool
	 */
	public function hasOpenPlatform();

	/**
	 * 获取默认微信公众号实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public function official(array $options = []);

	/**
	 * 微信公众号配置是否存在
	 *
	 * @return bool
	 */
	public function hasOfficial();

	/**
	 * 获取默认小程序实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application
	 */
	public function miniProgram(array $options = []);

	/**
	 * 小程序配置是否存在
	 *
	 * @return bool
	 */
	public function hasMiniProgram();

	/**
	 * 获取配置数据
	 *
	 * @param string $type
	 * @return array
	 */
	public function getConfig($type = null, $default = null);

}
