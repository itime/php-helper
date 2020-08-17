<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Auth\AuthManager;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;
use Xin\Thinkphp\Provider\ServiceProvider;

/**
 * Class AuthServiceProvider
 */
class AuthServiceProvider extends ServiceProvider{
	
	/**
	 * @inheritDoc
	 */
	public function register(){
		$this->registerAuthenticator();
		
		$this->registerGuard();
	}
	
	/**
	 * Register the authenticator services.
	 *
	 * @return void
	 */
	protected function registerAuthenticator(){
		$method = method_exists($this->config, 'pull') ? 'pull' : 'get';
		$auth = new AuthManager(
			$this->config->$method('auth')
		);
		
		$this->app->bindTo('auth', $auth);
	}
	
	/**
	 * 注册网关
	 */
	protected function registerGuard(){
		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');
		
		// 注册无状态守卫者
		$auth->extend('token', function($name, $config, UserProviderContract $provider){
			return new TokenGuard($name, $config, $provider);
		});
		
		// 注册有状态守卫者
		$auth->extend('session', function($name, $config, UserProviderContract $provider){
			return new SessionGuard($name, $config, $provider);
		});
		
		// 注册有状态守卫者
		$auth->extend('token_session', function($name, $config, UserProviderContract $provider){
			return new TokenSessionGuard($name, $config, $provider);
		});
	}
}
