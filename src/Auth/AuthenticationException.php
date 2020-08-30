<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth;

class AuthenticationException extends \Exception{
	
	/**
	 * @var string
	 */
	protected $guard;
	
	/**
	 * @var string
	 */
	protected $redirectTo;
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * AuthenticationException constructor.
	 *
	 * @param string $guard
	 * @param string $redirectTo
	 * @param array  $config
	 */
	public function __construct($guard, $redirectTo = '', array $config = []){
		parent::__construct('Unauthenticated.', -1);
		
		$this->guard = $guard;
		
		$this->redirectTo = is_null($redirectTo) ? '' : $redirectTo;
		if(empty($this->redirectTo) && isset($config['auth_url'])){
			$this->redirectTo = $config['auth_url'];
		}
		
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
	
	/**
	 * @return string
	 */
	public function redirectTo(){
		return $this->redirectTo;
	}
	
}
