<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use EasyWeChat\Factory;
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
		
		$config = $this->config['open'];
		$config = $this->checkConfig($config);
		
		return Factory::openPlatform($config);
	}
	
	/**
	 * @inheritDoc
	 */
	public function defaultOfficial(array $options = []){
		if(!isset($this->config['official']) || empty($this->config['official'])){
			throw new \RuntimeException("wechat config 'official' not defined.");
		}
		
		$config = $this->config['official'];
		$config = $this->checkConfig($config);
		
		return Factory::openPlatform($config);
	}
	
	/**
	 * @inheritDoc
	 */
	public function defaultMiniProgram(array $options = []){
		if(!isset($this->config['mini_program']) || empty($this->config['mini_program'])){
			throw new \RuntimeException("wechat config 'mini_program' not defined.");
		}
		
		$config = $this->config['mini_program'];
		$config = $this->checkConfig($config);
		
		return Factory::openPlatform($config);
	}
	
	/**
	 * 检查配置是否正确
	 *
	 * @param array $config
	 * @return array
	 */
	protected function checkConfig(array $config){
		if(!isset($config['appid']) || empty($config['appid'])){
			throw new \RuntimeException("wechat config 'appid' not defined.");
		}
		
		if(!isset($config['secret']) || empty($config['secret'])){
			throw new \RuntimeException("wechat config 'secret' not defined.");
		}
		
		return $config;
	}
}
