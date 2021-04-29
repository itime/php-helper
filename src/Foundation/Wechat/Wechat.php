<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Foundation\Wechat;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\ServiceContainer;
use Xin\Contracts\Foundation\Wechat as WechatContract;

class Wechat implements WechatContract{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * Wechat constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function openPlatform(array $options = []){
		if(!isset($this->config['open_platform']) || empty($this->config['open_platform'])){
			throw new WechatNotConfigureException("wechat config 'open_platform' not defined.");
		}

		$config = $this->checkConfig(
			$this->getConfig('open_platform')
		);

		return $this->initApplication(
			Factory::openPlatform($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function official(array $options = []){
		if(!isset($this->config['official']) || empty($this->config['official'])){
			throw new WechatNotConfigureException("wechat config 'official' not defined.");
		}

		$config = $this->checkConfig(
			$this->getConfig('official')
		);

		return $this->initApplication(
			Factory::officialAccount($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgram(array $options = []){
		if(!isset($this->config['miniprogram']) || empty($this->config['miniprogram'])){
			throw new WechatNotConfigureException("wechat config 'miniprogram' not defined.");
		}

		$config = $this->checkConfig(
			$this->getConfig('miniprogram')
		);

		return $this->initApplication(
			Factory::miniProgram($config),
			$options
		);
	}

	/**
	 * @param \EasyWeChat\Kernel\ServiceContainer $sc
	 * @param array                               $options
	 * @return mixed
	 */
	protected function initApplication(ServiceContainer $sc, array $options){
		//// 监听微信响应数据
		//$sc->events->addListener(HttpResponseCreated::class, function(HttpResponseCreated $event){
		//	$response = $event->response;
		//
		//	if(false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')){
		//		$response = StreamResponse::buildFromPsrResponse($response);
		//	}else{
		//		$response = Response::buildFromPsrResponse($response);
		//	}
		//});

		return $sc;
	}

	/**
	 * 检查配置是否正确
	 *
	 * @param array $config
	 * @return array
	 */
	protected function checkConfig(array $config){
		if(empty($config)){
			throw new WechatInvalidConfigException("wechat config is invalid.");
		}

		if(!isset($config['app_id']) || empty($config['app_id'])){
			throw new WechatInvalidConfigException("wechat config 'app_id' not defined.");
		}

		if(!isset($config['mode']) || $config['mode'] == 0){
			if(!isset($config['secret']) || empty($config['secret'])){
				throw new WechatInvalidConfigException("wechat config 'secret' not defined.");
			}
		}else{
			if(!isset($config['authorizer_refresh_token']) || empty($config['authorizer_refresh_token'])){
				throw new WechatInvalidConfigException("wechat config 'authorizer_refresh_token' not defined.");
			}
		}

		return $config;
	}

	/**
	 * 获取配置
	 *
	 * @param string|null $name
	 * @return mixed
	 */
	protected function getConfig($name = null){
		if(null === $name){
			return $this->config;
		}

		return isset($this->config[$name]) ? $this->config[$name] : null;
	}
}
