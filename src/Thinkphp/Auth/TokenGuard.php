<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\Request;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

/**
 * Class TokenUser
 */
class TokenGuard extends BasicGuard{
	
	/**
	 * @var \think\Cache
	 */
	protected $cache;
	
	/**
	 * @var \Closure
	 */
	protected $authTokenResolver;
	
	/**
	 * TokenUser constructor.
	 *
	 * @param string                           $name
	 * @param array                            $config
	 * @param \Xin\Contracts\Auth\UserProvider $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider){
		parent::__construct($name, $config, $provider);
		
		$this->cache = $this->app['cache'];
		
		$this->authTokenResolver = $this->getDefaultAuthTokenResolver();
	}
	
	/**
	 * @return string
	 */
	public function getAuthToken(){
		return call_user_func($this->authTokenResolver, $this->request, $this);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function updateSession($user){
		$this->cache->set($this->getAuthToken(), $user);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function resolveUser(){
		return $this->cache->get($this->getAuthToken());
	}
	
	/**
	 * @return \Closure
	 */
	protected function getDefaultAuthTokenResolver(){
		return function(Request $request){
			return $request->param('session_id');
		};
	}
	
	/**
	 * @inheritDoc
	 */
	public function logout(){
		parent::logout();
		$this->cache->rm($this->getAuthToken());
	}
}
