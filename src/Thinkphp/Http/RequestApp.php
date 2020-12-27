<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Http;

use Xin\Thinkphp\Facade\Auth;

trait RequestApp{
	
	/**
	 * @var \Closure
	 */
	protected $appResolverCallback;
	
	/**
	 * 设置应用完成器
	 *
	 * @param \Closure $resolverCallback
	 */
	public function setAppResolver(\Closure $resolverCallback){
		$this->appResolverCallback = $resolverCallback;
	}
	
	/**
	 * 获取当前应用信息
	 *
	 * @param string $field
	 * @param mixed  $default
	 * @param bool   $abort
	 * @return mixed
	 */
	public function app($field = null, $default = null, $abort = true){
		if(is_null($this->appResolverCallback)){
			$this->appResolverCallback = function($field = null, $default = null, $verifyType = 1){
				return Auth::getUser($field, $default, $verifyType);
			};
		}
		
		return call_user_func($this->appResolverCallback, $field, $default, $abort);
	}
}
