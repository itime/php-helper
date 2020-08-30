<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Auth\AuthManager;
use Xin\Contracts\Auth\Factory as AuthFactory;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;
use Xin\Support\Reflect;
use Xin\Thinkphp\Foundation\ServiceProvider;

class AuthServiceProvider extends ServiceProvider{
	
	/**
	 * @inheritDoc
	 */
	public function register(){
		$this->registerAuthManager();
		
		$this->registerGuards();
		
		$this->registerProviders();
		
		$this->registerRequestUserResolver();
	}
	
	/**
	 * Register the authenticator services.
	 *
	 * @return void
	 */
	protected function registerAuthManager(){
		$method = Reflect::methodVisible($this->config, 'pull') === Reflect::VISIBLE_PUBLIC ?
			'pull' : 'get';
		
		$auth = new AuthManager(
			$this->config->$method('auth')
		);
		
		if(Reflect::methodVisible($this->app, 'bindTo') === Reflect::VISIBLE_PUBLIC){
			$this->app->bindTo('auth', $auth);
		}else{
			$this->app->bind([
				'auth'             => $auth,
				AuthFactory::class => $auth,
			]);
		}
	}
	
	/**
	 * 注册守卫者
	 */
	protected function registerGuards(){
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
			return new SessionTokenGuard($name, $config, $provider);
		});
	}
	
	/**
	 * 注册数据提供者
	 */
	protected function registerProviders(){
		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');
		
		// Database Provider
		$auth->provider('database', function($config){
			return new DatabaseUserProvider(
				$this->app['db'], $config
			);
		});
		
		// Model Provider
		$auth->provider('model', function($config){
			return new ModelUserProvider($config);
		});
	}
	
	/**
	 * 注册Request用户完成器
	 */
	protected function registerRequestUserResolver(){
		$request = $this->app->request;
		if(!method_exists($request, 'setUserResolver')){
			return;
		}
		
		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');
		
		$request->setUserResolver(function($field, $default, $abort) use ($auth){
			return $auth->guard()->getUser($field, $default, $abort);
		});
	}
}
