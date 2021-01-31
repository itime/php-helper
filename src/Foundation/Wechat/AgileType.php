<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation\Wechat;

use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Http\Response;

/**
 * @mixin \EasyWeChat\Kernel\Support\Collection
 */
class AgileType implements Arrayable{
	
	/**
	 * @var \EasyWeChat\Kernel\Http\Response
	 */
	protected $raw = null;
	
	/**
	 * @var \EasyWeChat\Kernel\Support\Collection
	 */
	protected $result;
	
	/**
	 * AgileResponse constructor.
	 *
	 * @param \EasyWeChat\Kernel\Http\Response $response
	 */
	public function __construct(Response $response){
		$this->raw = $response;
		$this->result = $response->toCollection();
	}
	
	/**
	 * @inheritDoc
	 */
	public function toArray(){
		return $this->raw->toArray();
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset){
		return $this->result->offsetExists($offset);
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset){
		return $this->result->offsetGet($offset);
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value){
		$this->result->offsetSet($offset, $value);
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset){
		$this->result->offsetUnset($offset);
	}
	
	/**
	 * 验证是否通过
	 *
	 * @return bool
	 */
	public function isOk(){
		if(!$this->result->count()){
			return false;
		}
		
		if(isset($this->result['errcode']) && $this->result['errcode'] != 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 错误原因
	 *
	 * @return bool
	 */
	public function errmsg(){
		if(!$this->result->count()){
			return '';
		}
		
		if(isset($this->result['errmsg'])){
			return $this->result['errmsg'];
		}
		
		return '';
	}
	
	/**
	 * 获取错误码
	 *
	 * @return int
	 */
	public function errcode(){
		if(!$this->result->count()){
			return 0;
		}
		
		if(isset($this->result['errcode'])){
			return $this->result['errcode'];
		}
		
		return 0;
	}
	
	/**
	 * 条件执行
	 *
	 * @param callable      $resolve
	 * @param callable|null $reject
	 * @return mixed
	 */
	public function then(callable $resolve, callable $reject = null){
		if($this->isOk()){
			return $resolve($this);
		}else{
			return $reject && $reject($this);
		}
	}
	
	/**
	 * 如果验证不通过则抛出异常
	 *
	 * @param callable|null $resolve
	 * @return mixed
	 * @throws \Xin\Foundation\Wechat\WechatResponseException
	 */
	public function canThrowException(callable $resolve = null){
		if($this->isOk()){
			if($resolve){
				return $resolve($this);
			}
			
			return $this;
		}
		
		throw new WechatResponseException(
			$this->errmsg(), $this->errcode(),
			$this->raw
		);
	}
	
	/**
	 * @return \EasyWeChat\Kernel\Http\Response|null
	 */
	public function response(){
		return $this->raw;
	}
	
	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return false|mixed
	 */
	public function __call($name, $arguments){
		return call_user_func_array([$this->result, $name], $arguments);
	}
	
}
