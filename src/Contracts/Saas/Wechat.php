<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas;

use Xin\Contracts\Foundation\Wechat as BaseWechat;

interface Wechat extends BaseWechat{
	
	/**
	 * 获取id微信开放平台实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatformOfId(array $options = []);
	
	/**
	 * 获取应用id微信开放平台实例
	 *
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatformOfAppId(array $options = []);
	
	/**
	 * 根据id获取微信公众号实例
	 *
	 * @param int   $weappId
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application|\EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
	 */
	public function officialOfId($weappId, array $options = []);
	
	/**
	 * 根据应用id获取微信公众号实例
	 *
	 * @param int   $appId
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application|\EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
	 */
	public function officialOfAppId($appId, array $options = []);
	
	/**
	 * 根据id获取小程序实例
	 *
	 * @param int   $weappId
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function miniProgramOfId($weappId, array $options = []);
	
	/**
	 * 根据应用id获取小程序实例
	 *
	 * @param int   $appId
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function miniProgramOfAppId($appId, array $options = []);
}
