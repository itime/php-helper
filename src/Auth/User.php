<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth;

/**
 * Class User
 */
abstract class User implements UserInterface{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var mixed
	 */
	protected $user;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var \Xin\Auth\UserProviderInterface
	 */
	protected $provider;

	/**
	 * User constructor.
	 *
	 * @param                                           $name
	 * @param array                                     $config
	 * @param \Xin\Auth\UserProviderInterface           $provider
	 */
	public function __construct($name, array $config, UserProviderInterface $provider = null){
		$this->name = $name;
		$this->config = $config;
		$this->provider = $provider;
	}

	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function getUserInfo($field = null, $default = null, $abort = true){
		if(is_null($this->user)){
			$this->user = $this->resolveUser();
		}

		if($abort && is_null($this->user)){
			throw new AuthenticationException(
				$this->name,
				$this->config
			);
		}

		return empty($field) ? $this->user : (isset($this->user[$field]) ? $this->user[$field] : $default);
	}

	/**
	 * @return mixed
	 */
	abstract protected function resolveUser();

	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function getUserId($abort = true){
		return $this->getUserInfo('id', 0, $abort);
	}

	/**
	 * @inheritDoc
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function getUserPassword($abort = true){
		return $this->getUserInfo('password', false, $abort);
	}

	/**
	 * 缓存用户模型
	 *
	 * @param mixed $user
	 * @return string
	 */
	protected function makeAuthSign($user){
		return sha1(md5($user['id']).time());
	}

	/**
	 * @inheritDoc
	 */
	public function temporaryUser($user){
		$this->user = $user;
	}

	/**
	 * @inheritDoc
	 */
	public function logout(){
		$this->user = null;
	}

	/**
	 * 获取一个Session的唯一名称
	 *
	 * @return string
	 */
	public function getName(){
		return 'login_'.$this->name.'_'.sha1(static::class);
	}

}
