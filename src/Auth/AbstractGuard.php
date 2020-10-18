<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth;

use Xin\Contracts\Auth\Guard as GuardContract;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

abstract class AbstractGuard implements GuardContract{
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var mixed
	 */
	protected $user;
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * @var UserProviderContract
	 */
	protected $provider;
	
	/**
	 * @var \Closure
	 */
	protected $preCheckCallback;
	
	/**
	 * AbstractGuard constructor.
	 *
	 * @param string                    $name
	 * @param array                     $config
	 * @param UserProviderContract|null $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider = null){
		$this->name = $name;
		$this->config = $config;
		$this->provider = $provider;
	}
	
	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function getUser($field = null, $default = null, $abort = true){
		if(is_null($this->user)){
			$this->user = $this->resolveUser();
		}
		
		if($abort && is_null($this->user)){
			throw new AuthenticationException(
				$this->name,
				isset($this->config['auth_url']) ? $this->config['auth_url'] : '',
				$this->config
			);
		}
		
		if($abort){
			$this->preCheck($this->user);
		}
		
		return empty($field) ? $this->user : (isset($this->user[$field]) ? $this->user[$field] : $default);
	}
	
	/**
	 * 预检查数据
	 *
	 * @param mixed $user
	 */
	protected function preCheck($user){
		if(!$this->preCheckCallback){
			return;
		}
		
		call_user_func($this->preCheckCallback, $user);
	}
	
	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function getUserId($abort = true){
		return $this->getUser('id', 0, $abort);
	}
	
	/**
	 * @inheritDoc
	 */
	public function temporaryUser($user){
		$this->user = $user;
	}
	
	/**
	 * @inheritDoc
	 */
	public function check(){
		try{
			return $this->getUserId(false);
		}catch(AuthenticationException $e){
			return false;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function guest(){
		return !$this->check();
	}
	
	/**
	 * @return mixed
	 */
	abstract protected function resolveUser();
	
	/**
	 * @return array
	 */
	public function getConfig(){
		return $this->config;
	}
	
	/**
	 * 设置预检查回调
	 *
	 * @param callable $preCheckCallback
	 */
	public function setPreCheck(callable $preCheckCallback){
		$this->preCheckCallback = $preCheckCallback;
	}
}
