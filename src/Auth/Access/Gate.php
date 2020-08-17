<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Access;

use Xin\Contracts\Auth\Access\AuthenticRule;
use Xin\Contracts\Auth\Access\Gate as GateContract;
use Xin\Contracts\Auth\Guard;

class Gate implements GateContract{
	
	/**
	 * @var \Xin\Contracts\Auth\Guard
	 */
	private $user;
	
	/**
	 * 适配器
	 *
	 * @var array
	 */
	protected $adapters = [
	];
	
	/**
	 * 适配器实例列表
	 *
	 * @var array
	 */
	private $adapterInstances = [];
	
	/**
	 * Authentic constructor.
	 *
	 * @param Guard $user
	 */
	public function __construct(Guard $user){
		$this->user = $user;
	}
	
	/**
	 */
	protected function getUser(){
		return $this->user;
	}
	
	/**
	 * @inheritDoc
	 * @return mixed
	 * @throws \Xin\Auth\Access\NotFoundAdapterException
	 */
	public function checkAuth(AuthenticRule $rule){
		$adapter = $this->resolveAdapter($rule);
		return $adapter->checkAuth($rule);
	}
	
	/**
	 * 获取验证适配器
	 *
	 * @param \Xin\Contracts\Auth\Access\AuthenticRule $rule
	 * @return \Xin\Contracts\Auth\Access\Gate
	 * @throws \Xin\Auth\Access\NotFoundAdapterException
	 */
	private function resolveAdapter(AuthenticRule $rule){
		$ruleClass = get_class($rule);
		$adapter = $this->makeAdapter($ruleClass);
		if($adapter){
			return $adapter;
		}
		
		$scheme = $rule->getScheme();
		$adapter = $this->makeAdapter($scheme);
		if($adapter){
			return $adapter;
		}
		
		throw new NotFoundAdapterException(
			$scheme,
			$this->adapters
		);
	}
	
	/**
	 * 制作适配器
	 *
	 * @param string $name
	 * @return Gate
	 */
	private function makeAdapter($name){
		if(isset($this->adapterInstances[$name])){
			return $this->adapterInstances[$name];
		}
		
		if(isset($this->adapters[$name])){
			$adapterClass = $this->adapters[$name];
			$this->adapterInstances[$name] = new $adapterClass(
				$this->getUser(),
				$this->getCheckAdmin()
			);
			
			return $this->adapterInstances[$name];
		}
		
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkAdministrator($uid){
		// TODO: Implement checkAdministrator() method.
	}
}
