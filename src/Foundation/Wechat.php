<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation;

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
	public function defaultOpenPlatform(array $options = []){
		if(!isset($this->config['open']) || empty($this->config['open'])){
			throw new \RuntimeException("wechat config 'open' not defined.");
		}
		
		$config = $this->checkConfig(
			$this->getConfig('open')
		);
		
		return $this->initApplication(
			Factory::openPlatform($config),
			$options
		);
	}
	
	/**
	 * @inheritDoc
	 */
	public function defaultOfficial(array $options = []){
		if(!isset($this->config['official']) || empty($this->config['official'])){
			throw new \RuntimeException("wechat config 'official' not defined.");
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
	public function defaultMiniProgram(array $options = []){
		if(!isset($this->config['miniprogram']) || empty($this->config['miniprogram'])){
			throw new \RuntimeException("wechat config 'mini_program' not defined.");
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
		$options = array_merge([
			'strict' => true,
		], $options);
		
		//		$sc->events->dispatch();
		
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
			throw new \RuntimeException("wechat config is invalid.");
		}
		
		if(!isset($config['appid']) || empty($config['appid'])){
			throw new \RuntimeException("wechat config 'appid' not defined.");
		}
		
		if(!isset($config['mode']) || $config['mode'] == 0){
			if(!isset($config['secret']) || empty($config['secret'])){
				throw new \RuntimeException("wechat config 'secret' not defined.");
			}
		}else{
			if(!isset($config['authorizer_refresh_token']) || empty($config['authorizer_refresh_token'])){
				throw new \RuntimeException("wechat config 'authorizer_refresh_token' not defined.");
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
