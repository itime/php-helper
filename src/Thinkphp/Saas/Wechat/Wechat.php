<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use EasyWeChat\Factory;
use Xin\Contracts\Saas\WechatRepository;
use Xin\Foundation\Wechat\Wechat as WechatBase;
use Xin\Foundation\Wechat\WechatNotConfigureException;

class Wechat extends WechatBase implements WechatRepository{
	
	/**
	 * @var \closure
	 */
	protected $openPlatformCallback = null;
	
	/**
	 * @inheritDoc
	 */
	public function openPlatformOfId($id, array $options = []){
		if($id == 0){
			return $this->openPlatform($options);
		}
		
		if(!$this->openPlatformCallback){
			throw new \RuntimeException("未配置微信开放平台配置获取器");
		}
		
		$config = call_user_func($this->openPlatformCallback, $this, $id, 0);
		$config = $this->checkConfig($config);
		
		return $this->initApplication(
			Factory::openPlatform($config),
			$options
		);
	}
	
	/**
	 * @inheritDoc
	 */
	public function openPlatformOfAppId($appId, array $options = []){
		if($appId == 0){
			return $this->openPlatform($options);
		}
		
		if(!$this->openPlatformCallback){
			throw new \RuntimeException("未配置微信开放平台配置获取器");
		}
		
		$config = call_user_func($this->openPlatformCallback, $this, $appId, 1);
		$config = $this->checkConfig($config);
		
		return $this->initApplication(
			Factory::openPlatform($config),
			$options
		);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\db\exception\DbException
	 */
	public function officialOfId($id, array $options = []){
		if($id == 0){
			return $this->official($options);
		}
		
		$weapp = DatabaseWeapp::where('id', $id)->find();
		if(empty($weapp)){
			throw new WechatNotConfigureException("未配置或授权公众号！");
		}
		
		return $this->newOfficialInstance($weapp, $options);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\db\exception\DbException
	 */
	public function officialOfAppId($appId, array $options = []){
		if($appId == 0){
			return $this->official($options);
		}
		
		$weapp = DatabaseWeapp::where([
			'app_id'   => $appId,
			'app_type' => 1,
		])->find();
		if(empty($weapp)){
			throw new WechatNotConfigureException("未配置或授权公众号！");
		}
		
		return $this->newOfficialInstance($weapp, $options);
	}
	
	/**
	 * 解析公众号实例
	 *
	 * @param \Xin\Thinkphp\Saas\Wechat\DatabaseWeapp $weapp
	 * @param array                                   $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	protected function newOfficialInstance(DatabaseWeapp $weapp, array $options){
		$config = $this->resolveWeappConfig($weapp);
		
		if($config['mode'] === 1){
			$wApp = $this->openPlatformOfAppId($weapp['app_id'], $options['open_platform'] ?? []);
			$miniProgram = $wApp->officialAccount($config['appid'], $config['authorizer_refresh_token']);
		}else{
			$miniProgram = Factory::officialAccount($config);
		}
		
		return $this->initApplication($miniProgram, $options);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\db\exception\DbException
	 */
	public function miniProgramOfId($id, array $options = []){
		if($id == 0){
			return $this->miniProgram($options);
		}
		
		$weapp = DatabaseWeapp::where('id', $id)->find();
		if(empty($weapp)){
			throw new WechatNotConfigureException("未配置或授权小程序！");
		}
		
		return $this->newMiniProgramInstance($weapp, $options);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\db\exception\DbException
	 */
	public function miniProgramOfAppId($appId, array $options = []){
		if($appId == 0){
			return $this->miniProgram($options);
		}
		
		$weapp = DatabaseWeapp::where([
			'app_id'   => $appId,
			'app_type' => 0,
		])->find();
		if(empty($weapp)){
			throw new WechatNotConfigureException("未配置或授权小程序！");
		}
		
		return $this->newMiniProgramInstance($weapp, $options);
	}
	
	/**
	 * 解析小程序实例
	 *
	 * @param \Xin\Thinkphp\Saas\Wechat\DatabaseWeapp $weapp
	 * @param array                                   $options
	 * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
	 */
	protected function newMiniProgramInstance(DatabaseWeapp $weapp, array $options){
		$config = $this->resolveWeappConfig($weapp);
		
		if($config['mode'] === 1){
			$wApp = $this->openPlatformOfAppId($weapp['app_id'], $options['open_platform'] ?? []);
			$miniProgram = $wApp->miniProgram($config['appid'], $config['authorizer_refresh_token']);
		}else{
			$miniProgram = Factory::miniProgram($config);
		}
		
		return $this->initApplication($miniProgram, $options);
	}
	
	/**
	 * 解析应用配置信息
	 *
	 * @param \Xin\Thinkphp\Saas\Wechat\DatabaseWeapp $weapp
	 * @return array
	 */
	protected function resolveWeappConfig(DatabaseWeapp $weapp){
		return $this->checkConfig([
			'mode'                     => $weapp['mode'],
			'app_id'                   => $weapp['appid'],
			'secret'                   => $weapp['appsecret'],
			'authorizer_refresh_token' => $weapp['authorizer_refresh_token'],
		]);
	}
	
	/**
	 * 设置微信开放平台获取器
	 *
	 * @param \Closure|null $openPlatformCallback
	 */
	public function setOpenPlatformCallback(?\Closure $openPlatformCallback){
		$this->openPlatformCallback = $openPlatformCallback;
	}
	
}
