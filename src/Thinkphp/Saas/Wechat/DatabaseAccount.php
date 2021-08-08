<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use think\Model;
use Xin\Thinkphp\Foundation\Model\Appable;

/**
 * 微信账号模型
 *
 * @property-read int    app_type 应用类型：0 小程序，1 公众号
 * @property-read int    authorization_type 授权类型 0.手动配置 1.开放平台授权
 * @property-read string third_id 原始ID
 * @property-read string appid 小程序/公众号AppId
 * @property-read string secret 小程序/公众号Secret
 * @property-read string authorizer_refresh_token 小程序/公众号刷新令牌
 * @property-read array  authorizer_func_info 小程序/公众号权限信息
 * @property-read array  authorizer_func_info_arr 小程序/公众号权限信息（前台显示）
 * @property-read int    closed 是否解除授权
 */
class DatabaseAccount extends Model{

	use Appable;

	/**
	 * @var string
	 */
	protected $name = 'wechat_account';

	/**
	 * 获取小程序权限信息
	 *
	 * @return array
	 */
	protected function getAuthorizerFuncInfoAttr(){
		$authorizerInfo = $this->getData('authorizer_info');
		if(empty($authorizerInfo)){
			return [];
		}

		return isset($authorizerInfo['func_info']) ? $authorizerInfo['func_info'] : [];
	}

	/**
	 * 获取小程序权限信息（前台显示）
	 *
	 * @return array
	 */
	protected function getAuthorizerFuncInfoArrAttr(){
		$funcInfo = $this->getData('authorizer_func_info');

		$result = [];
		foreach($funcInfo as $item){
			$funcScopeId = $item['funcscope_category']['id'];
			$result[] = array_merge(AuthEnum::getDesc($funcScopeId), $item['confirm_info'] ?? []);
		}

		return $result;
	}

	/**
	 * 同步当前小程序信息
	 *
	 * @return $this
	 */
	public function sync(){
		if($this->getOrigin('authorization_type')){
			$data = $this->loadAuthorizeInfo();
			$this->save($data);
		}

		return $this;
	}

	/**
	 * 获取当前小程序授权信息
	 *
	 * @return array
	 */
	public function loadAuthorizeInfo(){
		return $this->getAuthorizerInfoByAppId($this->getOrigin('appid'));
	}

	/**
	 * 获取小程序授权信息
	 *
	 * @param string $appId
	 * @return array
	 */
	protected static function getAuthorizerInfoByAppId($appId){
		/** @var \EasyWeChat\OpenPlatform\Application $openPlatform */
		$openPlatform = app('wechat')->openPlatformOfAppId($appId);
		$result = $openPlatform->getAuthorizer($appId);

		if(isset($result['errcode']) && $result['errcode'] != 0){
			// 已取消授权
			if(61003 == $result['errcode']){
				return [
					'third_appid' => $appId,
					'closed'      => 1,
				];
			}

			throw new \LogicException($result['errmsg']);
		}

		$authorizerInfo = $result['authorizer_info'];
		$authorizationInfo = $result['authorization_info'];
		$authorizerRefreshToken = $authorizationInfo['authorizer_refresh_token'];
		$thirdAppid = $authorizationInfo['authorizer_appid'];

		return [
			'third_id'                 => $authorizerInfo['user_name'],
			'appid'                    => $thirdAppid,
			'nick_name'                => $authorizerInfo['nick_name'] ?? '',
			'head_img'                 => $authorizerInfo['head_img'] ?? '',
			'qrcode_url'               => $authorizerInfo['qrcode_url'] ?? '',
			'principal_name'           => $authorizerInfo['principal_name'] ?? '',
			'signature'                => $authorizerInfo['signature'] ?? '',
			'service_type'             => $authorizerInfo['service_type']['id'] ?? 0,
			'verify_type'              => $authorizerInfo['verify_type']['id'] ?? -1,
			'business_info'            => json_encode(
				$authorizerInfo['business_info'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			),
			'miniprogram_info'         => json_encode(
				$authorizerInfo['MiniProgramInfo'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			),
			'authorizer_refresh_token' => $authorizerRefreshToken,
			'authorization_info'       => json_encode(
				$authorizationInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			),
			'closed'                   => 0,
		];
	}

	/**
	 * 快速创建
	 *
	 * @param array $data
	 * @return static|\think\Model
	 */
	public static function fastCreate(array $data = []){
		if(isset($data['authorization_type']) && $data['authorization_type'] == 1){
			$data = array_merge(self::getAuthorizerInfoByAppId($data['appid']), $data);
		}

		$data = array_merge([
			'third_id'                 => '',
			'nick_name'                => '',
			'head_img'                 => '',
			'qrcode_url'               => '',
			'principal_name'           => '',
			'signature'                => '',
			'authorizer_refresh_token' => '',
			'authorizer_info'          => '',
			'business_info'            => '',
			'miniprogram_info'         => '',
		], $data);

		return self::create($data);
	}
}
