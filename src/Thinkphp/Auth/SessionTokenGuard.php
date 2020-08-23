<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Auth\AbstractStatefulGuard;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

class SessionTokenGuard extends AbstractStatefulGuard{
	
	use EventHelpers, TokenGuardHelpers;
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var \think\Request
	 */
	protected $request;
	
	/**
	 * @var \think\Session
	 */
	protected $session;
	
	/**
	 * @var bool
	 */
	private $sessionInit = false;
	
	/**
	 * TokenSessionUser constructor.
	 *
	 * @param string                           $name
	 * @param array                            $config
	 * @param \Xin\Contracts\Auth\UserProvider $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider){
		parent::__construct($name, $config, $provider);
		
		$this->app = Container::get('app');
		
		$this->request = $this->app['request'];
		$this->session = $this->app['session'];
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
		if(!$this->sessionInit){
			$this->session = $this->app['session'];
			$this->session->init([
				'id' => $this->getAuthToken(),
			]);
		}
		
		return $this->session;
	}
}
