<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Http;

trait RequestUser{
	
	/**
	 * @var \Closure
	 */
	protected $userResolverCallback;
	
	/**
	 * 设置用户完成器
	 *
	 * @param \Closure $resolverCallback
	 */
	public function setUserResolver(\Closure $resolverCallback){
		$this->userResolverCallback = $resolverCallback;
	}
	
	/**
	 * 获取用户信息
	 *
	 * @param string $field
	 * @param mixed  $default
	 * @param bool   $abort
	 * @return mixed
	 */
	public function user($field = null, $default = null, $abort = true){
		return call_user_func($this->userResolverCallback, $field, $default, $abort);
	}
}
