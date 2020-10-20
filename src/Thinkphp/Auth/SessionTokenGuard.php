<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\Container;
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
	 * SessionTokenGuard constructor.
	 *
	 * @param string                    $name
	 * @param array                     $config
	 * @param UserProviderContract|null $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider = null){
		parent::__construct($name, $config, $provider);
		
		$this->app = Container::getInstance();
		
		$this->request = $this->app['request'];
		$this->session = $this->app['session'];
	}
	
	/**
	 * @inheritDoc
	 */
	protected function updateSession($user){
		$this->session()->set('user', $user);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function resolveUser(){
		return $this->session()->get('user');
	}
	
	/**
	 * @inheritDoc
	 */
	public function logout(){
		parent::logout();
		
		$this->session()->destroy();
	}
	
	/**
	 * 获取Session实例
	 *
	 * @return \think\Session
	 */
	protected function session(){
		if(!$this->sessionInit){
			$this->session = $this->app['session'];
			$this->session->setId($this->getAuthToken());
			//			$this->session->init([
			//				'id' => $this->getAuthToken(),
			//			]);
		}
		
		return $this->session;
	}
}
