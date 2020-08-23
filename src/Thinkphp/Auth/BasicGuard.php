<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Auth;

use think\Container;
use Xin\Auth\Guard;
use Xin\Auth\LoginException;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

/**
 * Class BasicUser
 */
abstract class BasicGuard extends Guard{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var \think\Request
	 */
	protected $request;
	
	/**
	 * BasicUser constructor.
	 *
	 * @param string               $name
	 * @param array                $config
	 * @param UserProviderContract $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider){
		parent::__construct($name, $config, $provider);
		
		$this->app = Container::get('app');
		$this->request = $this->app['request'];
	}
	
	/**
	 * @inheritDoc
	 */
	public function temporaryUser($user, $abort = true){
		if(!$this->getUserInfo(null, null, $abort)){
			return;
		}
		
		$this->updateSession($user);
		
		parent::temporaryUser($user);
	}
	
	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function saveUserInfo(array $data, $abort = true){
		if(!$this->getUserInfo(null, null, $abort)){
			return;
		}
		
		$this->user->save($data);
		
		$this->temporaryUser($this->user);
	}
	
	/**
	 * 更新用户身份信息
	 *
	 * @param mixed $user
	 * @return mixed
	 */
	abstract protected function updateSession($user);
	
	/**
	 * @inheritDoc
	 */
	public function login($user){
		$this->updateSession($user);
		$this->user = $user;
		
		return $user;
	}
	
	/**
	 * @inheritDoc
	 */
	public function loginUsingId($id){
		$user = $this->provider->getById($id);
		if(empty($user)){
			throw new LoginException('用户不存在!', 40401);
		}
		
		return $this->login($user);
	}
	
	/**
	 * @inheritDoc
	 */
	public function loginUsingCredential(array $credentials, \Closure $notExistCallback = null){
		$user = $this->credentials($credentials, $notExistCallback);
		
		return $this->login($user);
	}
	
	/**
	 * @inheritDoc
	 */
	public function loginUsingPassword($field, $credential, $password){
		try{
			$user = $this->credentials([
				$field => $credential,
			]);
			
			if(!$this->provider->validatePassword($user, $password)){
				throw new LoginException('账号密码不正确', 40001);
			}
			
			return $this->login($user);
		}catch(LoginException $e){
			if($e->getCode() === 40402){
				throw new LoginException('账号密码不正确！', $e->getCode(), $e);
			}
			
			throw $e;
		}
	}
	
	/**
	 * @param array         $credentials
	 * @param \Closure|null $notExistCallback
	 * @return mixed
	 */
	protected function credentials(array $credentials, \Closure $notExistCallback = null){
		try{
			$this->provider->validateCredentials($credentials);
		}catch(\Exception $e){
			throw new LoginException($e->getMessage(), $e->getCode(), $e);
		}
		
		$user = $this->provider->getByCredentials($credentials);
		if(empty($user)){
			if(is_callable($notExistCallback)){
				$user = call_user_func($notExistCallback, $credentials);
			}else{
				throw new LoginException("用户不存在！", 40402);
			}
		}
		
		return $user;
	}
	
}
