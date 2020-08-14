<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth;

class AuthenticationException extends \Exception{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var string
	 */
	protected $guard;

	/**
	 * AuthenticationException constructor.
	 *
	 * @param string $guard
	 * @param array  $config
	 * @param string $message
	 */
	public function __construct($guard, array $config, $message = 'Unauthenticated.'){
		parent::__construct($message, -1);

		$this->guard = $guard;
		$this->config = $config;
	}

	/**
	 * @return array
	 */
	public function getConfig(){
		return $this->config;
	}

	/**
	 * @return string
	 */
	public function getGuard(){
		return $this->guard;
	}

}
