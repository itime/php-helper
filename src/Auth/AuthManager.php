<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth;

use Closure;
use InvalidArgumentException;
use Xin\Contracts\Auth\Factory as FactoryContract;
use Xin\Contracts\Auth\Guard as GuardContract;

/**
 * Class AuthManager
 * @method mixed getUser($field = null, $default = null, $verifyType = 1)
 * @method int getUserId($verifyType = 1)
 * @method mixed temporaryUser($user)
 * @method bool check()
 * @method bool guest()
 * @method bool login($user)
 * @method bool loginUsingId($id)
 * @method bool loginUsingCredential(array $credentials, \Closure $notExistCallback = null)
 * @method void setPreCheckCallback(\Closure $preCheckCallback)
 */
class AuthManager implements FactoryContract{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * 守卫者列表
	 *
	 * @var array
	 */
	protected $guards = [];

	/**
	 * 自定义驱动器
	 *
	 * @var array
	 */
	protected $customCreators = [];

	/**
	 * 自定义用户提供者
	 *
	 * @var array
	 */
	protected $customProviderCreators = [];

	/**
	 * AuthManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function guard($name = null){
		$name = $name ?: $this->getDefaultDriver();

		if(!isset($this->guards[$name])){
			$this->guards[$name] = $this->resolve($name);
		}

		return $this->guards[$name];
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUse($name){
		$name = $name ?: $this->getDefaultDriver();

		$this->setDefaultDriver($name);
	}

	/**
	 * 解决给定守卫者
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Auth\Guard
	 * @throws \InvalidArgumentException
	 */
	protected function resolve($name){
		$config = $this->getGuardConfig($name);

		if(is_null($config)){
			throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
		}

		if(isset($this->customCreators[$config['driver']])){
			return $this->callCustomCreator($name, $config);
		}

		$driverMethod = 'create'.ucfirst($config['driver']).'Driver';
		if(method_exists($this, $driverMethod)){
			return $this->{$driverMethod}($name, $config);
		}

		throw new InvalidArgumentException(
			"Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
		);
	}

	/**
	 * 调用一个自定义驱动创建器
	 *
	 * @param string $name
	 * @param array  $config
	 * @return mixed
	 */
	protected function callCustomCreator($name, array $config){
		return $this->customCreators[$config['driver']](
			$name, $config,
			$this->createUserProvider($config['provider'])
		);
	}

	/**
	 * 获取默认的提供者
	 *
	 * @return string
	 */
	public function getDefaultUserProvider(){
		return $this->config['defaults']['provider'];
	}

	/**
	 * 获取用户提供者配置
	 *
	 * @param string $provider
	 * @return array|void
	 */
	public function getProviderConfiguration($provider){
		if($provider = $provider ?: $this->getDefaultUserProvider()){
			if(!isset($this->config['providers'][$provider])){
				throw new \RuntimeException(
					"auth config provider [{$provider}] not defined."
				);
			}

			return $this->config['providers'][$provider];
		}
	}

	/**
	 * 创建用户提供者
	 *
	 * @param string|null $provider
	 * @return \Xin\Contracts\Auth\UserProvider
	 */
	public function createUserProvider($provider = null){
		if(is_null($config = $this->getProviderConfiguration($provider))){
			throw new InvalidArgumentException(
				"Authentication user provider [{$provider}] is not defined."
			);
		}

		$driver = isset($config['driver']) ? $config['driver'] : null;
		if(isset($this->customProviderCreators[$driver])){
			return call_user_func(
				$this->customProviderCreators[$driver],
				$config
			);
		}

		$driverMethod = 'create'.ucfirst($config['driver']).'Provider';
		if(method_exists($this, $driverMethod)){
			return $this->{$driverMethod}($config);
		}

		return new $config['use']();
	}

	/**
	 * 获取默认的守卫者
	 *
	 * @return string
	 */
	public function getDefaultDriver(){
		return $this->config['defaults']['guard'];
	}

	/**
	 * 设置默认的守卫者
	 *
	 * @param string $name
	 */
	public function setDefaultDriver($name){
		$this->config['defaults']['guard'] = $name;
	}

	/**
	 * 设置一个守卫者实例
	 *
	 * @param string        $name
	 * @param GuardContract $user
	 * @return $this
	 */
	public function setGuard($name, GuardContract $user){
		$this->guards[$name] = $user;

		return $this;
	}

	/**
	 * 获取守卫者配置信息
	 *
	 * @param string $name
	 * @return array
	 */
	public function getGuardConfig($name){
		if(!isset($this->config['guards'][$name])){
			throw new \RuntimeException(
				"not configure [{$name}] guard."
			);
		}

		return $this->config['guards'][$name];
	}

	/**
	 * 注册一个驱动创建器
	 *
	 * @param string   $driver
	 * @param \Closure $callback
	 * @return \Xin\Auth\AuthManager
	 */
	public function extend($driver, Closure $callback){
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	/**
	 * 注册一个用户提供者
	 *
	 * @param string   $name
	 * @param \Closure $callback
	 * @return \Xin\Auth\AuthManager
	 */
	public function provider($name, Closure $callback){
		$this->customProviderCreators[$name] = $callback;

		return $this;
	}

	/**
	 * 获取用户信息
	 *
	 * @return mixed
	 */
	public function user(){
		return $this->getUser(null, null, 0);
	}

	/**
	 * 获取当前用户的ID
	 *
	 * @return int
	 */
	public function id(){
		return $this->getUserId(0);
	}

	/**
	 * 当前用户是否为超级管理员
	 *
	 * @return bool
	 */
	public function isAdministrator(){
		$config = $this->getGuardConfig(
			$this->getDefaultDriver()
		);
		return $this->id() === $config['administrator_id'];
	}

	/**
	 * 动态调用默认驱动的方法
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters){
		return $this->guard()->{$method}(...$parameters);
	}
}
