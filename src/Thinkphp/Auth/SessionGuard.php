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

class SessionGuard extends AbstractStatefulGuard{
	
	use EventHelpers;
	
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
	 * @var \think\Cookie
	 */
	protected $cookie;
	
	/**
	 * SessionUser constructor.
	 *
	 * @param string                           $name
	 * @param array                            $config
	 * @param \Xin\Contracts\Auth\UserProvider $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider){
		parent::__construct($name, $config, $provider);
		
		$this->app = Container::getInstance();
		
		$this->request = $this->app['request'];
		$this->session = $this->app['session'];
		$this->cookie = $this->app['cookie'];
	}
	
	/**
	 * 缓存用户模型
	 *
	 * @param array $user
	 */
	protected function updateSession($user){
		$userAuthSign = $this->makeAuthSign($user);
		
		$this->session->set($this->getName(), $user);
		
		$this->session->set($this->getName().'_auth_sign', $userAuthSign);
		$this->cookie->set($this->getName().'_auth_sign', $userAuthSign);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function resolveUser(){
		$authSignKey = $this->getName().'_auth_sign';
		
		$sessionAuthSign = $this->session->get($authSignKey);
		$cookieAuthSign = $this->cookie->get($authSignKey);
		
		if($sessionAuthSign == $cookieAuthSign){
			return $this->session->get($this->getName());
		}
		
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function logout(){
		parent::logout();
		
		$this->session->delete($this->getName());
	}
	
}
