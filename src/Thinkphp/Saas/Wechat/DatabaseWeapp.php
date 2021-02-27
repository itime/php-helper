<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use think\Model;
use Xin\Thinkphp\Saas\Model\Appable;

/**
 * @property-read int    app_type 应用类型：0 小程序，1 公众号
 * @property-read string third_id 原始ID
 * @property-read string appid 小程序/公众号AppId
 * @property-read string secret 小程序/公众号Secret
 * @property-read string authorizer_refresh_token 小程序/公众号刷新令牌
 * @property-read array  authorization_func_info 小程序/公众号权限信息
 * @property-read array  authorization_func_info_arr 小程序/公众号权限信息（前台显示）
 * @property-read int    authorization_type 授权类型 0.手动配置 1.开放平台授权
 * @property-read int    closed 是否解除授权
 */
class DatabaseWeapp extends Model{
	
	use Appable;
	
	/**
	 * @var string
	 */
	protected $name = 'wechat_weapp';
	
	/**
	 * 获取小程序权限信息
	 *
	 * @return array
	 */
	protected function getAuthorizationFuncInfoArrAttr(){
		$funcInfo = $this->getData('func_info');
		$funcInfo = (array)json_decode($funcInfo, true);
		
		$result = [];
		foreach($funcInfo as $item){
			$funcScopeId = $item['funcscope_category']['id'];
			$result[] = array_merge(WeappAuthEnum::getDesc($funcScopeId), $item['confirm_info'] ?? []);
		}
		
		return $result;
	}
	
	/**
	 * 同步当前小程序信息
	 *
	 * @return $this
	 */
	public function sync(){
		if($this->authorization_type){
			$data = $this->authorizeInfo();
			$this->save($data);
		}
		
		return $this;
	}
	
	/**
	 * 获取当前小程序授权信息
	 *
	 * @return array
	 */
	public function authorizeInfo(){
		return $this->getAuthorizerInfoByAppId($this->appid);
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
		
		return [
			'third_id'                 => $authorizerInfo['user_name'],
			'nick_name'                => $authorizerInfo['nick_name'] ?? '',
			'head_img'                 => $authorizerInfo['head_img'] ?? '',
			'qrcode_url'               => $authorizerInfo['qrcode_url'] ?? '',
			'principal_name'           => $authorizerInfo['principal_name'] ?? '',
			'signature'                => $authorizerInfo['signature'] ?? '',
			'authorizer_refresh_token' => $authorizerRefreshToken,
			'authorizer_info'          => json_encode(
				$authorizerInfo,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			),
			'authorization_func_info'  => json_encode(
				$authorizationInfo['func_info'],
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
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
			$data = array_merge(
				self::getAuthorizerInfoByAppId($data['appid']), $data
			);
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
			'authorization_func_info'  => '',
		], $data);
		
		return self::create($data);
	}
}
