<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\App;
use Xin\Auth\Access\Gate;
use Xin\Auth\AuthManager;
use Xin\Contracts\Auth\Access\Gate as GateContract;
use Xin\Contracts\Auth\Factory as AuthFactory;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;
use Xin\Support\Reflect;
use Xin\Thinkphp\Foundation\ServiceProvider;

class AuthServiceProvider extends ServiceProvider{
	
	/**
	 * @var callable
	 */
	protected $userResolver;
	
	/**
	 * @inheritDoc
	 */
	public function register(){
		$this->registerAuthManager();
		
		$this->registerGuards();
		
		$this->registerProviders();
		
		$this->registerRequestUserResolver();
		
		$this->registerAccessGate();
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
			$this->app->bindTo(AuthFactory::class, $auth);
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
		
		$request->setUserResolver($this->userResolver());
	}
	
	/**
	 * 注册授权服务
	 *
	 * @return void
	 */
	protected function registerAccessGate(){
		$this->app->bind('gate', GateContract::class);
		$this->app->bind(GateContract::class, function(App $app){
			$gate = new Gate($app, function() use ($app){
				return $app['auth']->guard()->getUser(null, null, false);
			});
			
			//			$routeClass = \Xin\Thinkphp\Auth\Access\Abilities\RouteCheck::class;
			//			$app->bind('ability_route', $routeClass);
			//			$gate->define('route', "ability_route@handle");
			
			return $gate;
		});
	}
	
	/**
	 * 用户解析器
	 *
	 * @return callable|\Closure
	 */
	protected function userResolver(){
		if(!$this->userResolver){
			/** @var AuthManager $auth */
			$auth = $this->app->make('auth');
			$this->userResolver = function($field = null, $default = null, $abort = true) use ($auth){
				return $auth->guard()->getUser($field, $default, $abort);
			};
		}
		
		return $this->userResolver;
	}
}
