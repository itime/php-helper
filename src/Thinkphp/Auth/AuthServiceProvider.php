<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\App;
use think\Service;
use Xin\Auth\Access\Gate;
use Xin\Auth\AuthManager;
use Xin\Contracts\Auth\Access\Gate as GateContract;
use Xin\Contracts\Auth\Factory as AuthFactory;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;
use Xin\Thinkphp\Auth\Access\CheckForRoute;

class AuthServiceProvider extends Service {

	/**
	 * @var callable
	 */
	protected $userResolver;

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->registerAuthManager();

		$this->registerGuards();

		$this->registerProviders();

		$this->registerRequestUserResolver();

		$this->registerAccessGate();

		$this->registerPolicies();
	}

	/**
	 * Register the authenticator services.
	 *
	 * @return void
	 */
	protected function registerAuthManager() {
		$this->app->bind([
			'auth' => AuthFactory::class,
			AuthFactory::class => function () {
				return new AuthManager(
					$this->app->config->get('auth')
				);
			},
		]);
	}

	/**
	 * 注册守卫者
	 */
	protected function registerGuards() {
		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');

		// 注册无状态守卫者
		$auth->extend('token', function ($name, $config, UserProviderContract $provider) {
			return new TokenGuard($name, $config, $provider);
		});

		// 注册有状态守卫者
		$auth->extend('session', function ($name, $config, UserProviderContract $provider) {
			return new SessionGuard($name, $config, $provider);
		});

		// 注册有状态守卫者
		$auth->extend('token_session', function ($name, $config, UserProviderContract $provider) {
			return new SessionTokenGuard($name, $config, $provider);
		});
	}

	/**
	 * 注册数据提供者
	 */
	protected function registerProviders() {
		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');

		// Database Provider
		$auth->provider('database', function ($config) {
			return new DatabaseUserProvider(
				$this->app['db'], $config
			);
		});

		// Model Provider
		$auth->provider('model', function ($config) {
			return new ModelUserProvider($config);
		});
	}

	/**
	 * 注册Request用户完成器
	 */
	protected function registerRequestUserResolver() {
		$request = $this->app->request;
		if (!method_exists($request, 'setUserResolver')) {
			return;
		}

		/** @var AuthManager $auth */
		$auth = $this->app->make('auth');
		$request->setUserResolver(function ($field = null, $default = null, $abort = true) use ($auth) {
			return $auth->guard()->getUser($field, $default, $abort);
		});
	}

	/**
	 * 注册授权服务
	 *
	 * @return void
	 */
	protected function registerAccessGate() {
		$this->app->bind('gate', GateContract::class);
		$this->app->bind(GateContract::class, Gate::class);
		$this->app->bind(Gate::class, function (App $app) {
			return new Gate($app, function () use ($app) {
				return $app['auth']->guard()->getUser(null, null, false);
			});
		});
	}

	/**
	 * 注册授权策略
	 *
	 * @return void
	 */
	protected function registerPolicies() {
		$this->app->bind('abilities.route', CheckForRoute::class);

		/** @var Gate $gate */
		$gate = $this->app->make('gate');
		$gate->define('route', "abilities.route@handle");
	}

}
