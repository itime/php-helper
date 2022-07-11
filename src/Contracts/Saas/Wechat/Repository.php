<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas\Wechat;

use Xin\Contracts\Wechat\Factory as WechatFactory;

interface Repository extends WechatFactory
{

	/**
	 * 锁定 appId
	 * @param int $appId
	 * @return void
	 */
	public function shouldUseOfAppId($appId);

	/**
	 * 锁定 开放平台 id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseOfOpenPlatformId($id);

	/**
	 * 获取id微信开放平台实例
	 *
	 * @param int $id
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatformOfId($id, array $options = []);

	/**
	 * 获取应用id微信开放平台实例
	 *
	 * @param int $appId
	 * @param string $name
	 * @param array $options
	 * @return \EasyWeChat\OpenPlatform\Application
	 */
	public function openPlatformOfAppId($appId, $name = null, array $options = []);

	/**
	 * 锁定 公众号 id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseOfOfficialAccountOfId($id);

	/**
	 * 根据id获取微信公众号实例
	 *
	 * @param int $id
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application|\EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
	 */
	public function officialAccountOfId($id, array $options = []);

	/**
	 * 根据应用id获取微信公众号实例
	 *
	 * @param int $appId
	 * @param string $name
	 * @param array $options
	 * @return \EasyWeChat\OfficialAccount\Application|\EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
	 */
	public function officialAccountOfAppId($appId, $name = null, array $options = []);

	/**
	 * 锁定 小程序 id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseMiniProgramOfId($id);

	/**
	 * 根据id获取小程序实例
	 *
	 * @param       $id
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function miniProgramOfId($id, array $options = []);

	/**
	 * 根据应用id获取小程序实例
	 *
	 * @param int $appId
	 * @param null $name
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function miniProgramOfAppId($appId, $name = null, array $options = []);

	/**
	 * 锁定 企业微信 id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseWorkId($id);

	/**
	 * 根据id获取企业微信实例
	 *
	 * @param string $id
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function workOfId($id, array $options = []);

	/**
	 * 根据应用id获取企业微信实例
	 *
	 * @param int $appId
	 * @param string $name
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function workOfAppId($appId, $name = null, array $options = []);

	/**
	 * 锁定 开放平台企业微信 id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseOpenWorkId($id);

	/**
	 * 根据id获取企业微信开放平台实例
	 *
	 * @param string $id
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function openWorkOfId($id, array $options = []);

	/**
	 * 根据应用id获取企业微信开放平台实例
	 *
	 * @param int $appId
	 * @param string $name
	 * @param array $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	public function openWorkOfAppId($appId, $name = null, array $options = []);

}
