<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Contracts\Auth\UserProvider as UserProviderContract;

class TokenSessionGuard extends TokenGuard{
	
	/**
	 * @var \think\Session
	 */
	protected $session;
	
	/**
	 * TokenSessionUser constructor.
	 *
	 * @param string                           $name
	 * @param array                            $config
	 * @param \Xin\Contracts\Auth\UserProvider $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider){
		parent::__construct($name, $config, $provider);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\Exception
	 */
	protected function updateSession($user){
		$this->session()->set('user', $user);
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\Exception
	 */
	protected function resolveUser(){
		return $this->session()->get('user');
	}
	
	/**
	 * @inheritDoc
	 * @throws \think\Exception
	 */
	public function logout(){
		parent::logout();
		$this->session()->destroy();
	}
	
	/**
	 * 获取Session实例
	 *
	 * @return \think\Session
	 * @throws \think\Exception
	 */
	protected function session(){
		if(is_null($this->session)){
			$this->session = $this->app['session'];
			$this->session->init([
				'id' => $this->getAuthToken(),
			]);
		}
		
		return $this->session;
	}
}
