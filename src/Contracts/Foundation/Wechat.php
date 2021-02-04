<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface Wechat{
	
	/**
	 * 获取默认微信开放平台实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatform(array $options = []);
	
	/**
	 * 获取默认微信公众号实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public function official(array $options = []);
	
	/**
	 * 获取默认小程序实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application
	 */
	public function miniProgram(array $options = []);
}
