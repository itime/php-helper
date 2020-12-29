<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Http;

trait RequestApp{
	
	/**
	 * @var mixed
	 */
	protected $xApp;
	
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
	 * @return mixed
	 */
	public function app($field = null, $default = null){
		if(is_null($this->appResolverCallback)){
			throw new \RuntimeException('not defined app getter.');
		}
		
		if(is_null($this->xApp)){
			$this->xApp = call_user_func($this->appResolverCallback, $this);
		}
		
		return empty($field) ? $this->xApp : (isset($this->xApp[$field]) ? $this->xApp[$field] : $default);
	}
	
	/**
	 * 获取当前应用ID
	 *
	 * @return int
	 */
	public function appId(){
		return $this->app('id', 0);
	}
}
