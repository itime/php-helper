<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Support;

class WechatResult implements \ArrayAccess{
	
	/**
	 * @var mixed
	 */
	protected $result = null;
	
	/**
	 * @var \Throwable
	 */
	protected $throw = null;
	
	/**
	 * @var \Throwable
	 */
	protected $exception = null;
	
	/**
	 * WechatResult constructor.
	 *
	 * @param mixed $result
	 */
	protected function __construct($result){
		if(is_callable($result)){
			try{
				$result = call_user_func_array($result, []);
			}catch(\Throwable $e){
				$this->exception = $e;
				$result = null;
			}
		}elseif($result instanceof Retry){
			try{
				$result = $result->invoke();
			}catch(\Throwable $e){
				$this->exception = $e;
				$result = null;
			}
		}
		
		$this->result = $result;
	}
	
	/**
	 * 验证数据是否有效
	 *
	 * @return bool
	 */
	public function isValid(){
		if(!$this->result){
			return false;
		}
		
		if(!isset($this->result['errcode'])){
			return true;
		}
		
		return $this->result['errcode'] == 0;
	}
	
	/**
	 * 获取错误码
	 *
	 * @return int
	 */
	public function getErrCode(){
		if(!$this->result){
			return -1;
		}
		
		return $this->result['errcode'];
	}
	
	/**
	 * 获取错误消息
	 *
	 * @return string
	 */
	public function getErrMsg(){
		if(!$this->result){
			return '';
		}
		
		return $this->result['errmsg'];
	}
	
	/**
	 * 是否异常
	 *
	 * @return bool
	 */
	public function isException(){
		return $this->exception != null;
	}
	
	/**
	 * 获取异常类
	 *
	 * @return \Exception|\Throwable|null
	 */
	public function getException(){
		return $this->exception;
	}
	
	/**
	 * 抛出捕获的异常
	 *
	 * @return static
	 * @throws \Throwable
	 */
	public function throwException(){
		if($this->exception){
			throw $this->exception;
		}
		
		return $this;
	}
	
	/**
	 * 验证数据是否有效，无效则抛出异常
	 *
	 * @param bool $throw
	 * @return $this
	 */
	public function throw($throw = true){
		if($throw === false){
			$this->throw = null;
		}else{
			$this->throw = $throw === true ? '\\LogicException' : $throw;
		}
		return $this;
	}
	
	/**
	 * 验证数据有效性
	 *
	 * @param callable|null $resolve
	 * @param callable|null $reject
	 * @return mixed
	 */
	public function then(callable $resolve = null, callable $reject = null){
		if($this->isValid()){
			if(is_callable($resolve)){
				return call_user_func_array($resolve, [$this->result]);
			}
		}else{
			if(is_callable($reject)){
				return call_user_func_array($reject, [$this->result]);
			}
		}
		
		return $this->result;
	}
	
	/**
	 * 如果数据合法则返回指定的字段
	 *
	 * @param string|array $fields
	 * @param mixed        $default
	 * @return mixed
	 */
	public function result($fields = null, $default = null){
		return $this->then(function() use ($fields){
			if($fields === null){
				$result = $this->result;
				
				unset($result['errcode'], $result['errmsg']);
				
				return $result;
			}elseif(is_array($fields)){
				$result = [];
				
				foreach($fields as $field){
					if(isset($this->result[$field])){
						$result[$field] = $this->result[$field];
					}
				}
				
				return $result;
			}else{
				return isset($this->result[$fields]) ? $this->result[$fields] : null;
			}
		}, function() use ($default){
			if($this->throw){
				$exception = $this->throw;
				$errMsg = $this->getErrMsg();
				throw new $exception($errMsg ? $errMsg : '数据错误！');
			}
			
			return $default;
		});
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name){
		return isset($this->result[$name]);
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->result[$name];
	}
	
	/**
	 * 实例化
	 *
	 * @param mixed $result
	 * @return static
	 */
	public static function make($result){
		return new static($result);
	}
	
	/**
	 * 验证数据是否有效
	 *
	 * @param mixed $result
	 * @return bool
	 */
	public static function valid($result){
		return self::make($result)->isValid();
	}
	
	/**
	 * 实例化并包含抛出异常行为
	 *
	 * @param mixed $result
	 * @param bool  $exception
	 * @return static
	 */
	public static function toThrow($result, $exception = true){
		return static::make($result)->throw($exception);
	}
	
	/**
	 * 实例化并包含返回结果行为
	 *
	 * @param mixed        $result
	 * @param string|array $fields
	 * @param mixed        $default
	 * @return mixed
	 */
	public static function toResult($result, $fields = null, $default = null){
		return self::make($result)->result($fields, $default);
	}
	
	/**
	 * 实例化并包含抛出异常和返回结果行为
	 *
	 * @param mixed        $result
	 * @param string|array $fields
	 * @param bool         $exception
	 * @return mixed
	 */
	public static function toThrowsResult($result, $fields = null, $exception = true){
		return self::make($result)->throw($exception)->result($fields);
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset){
		return isset($this->result[$offset]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset){
		return $this->result[$offset];
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value){
		$this->result[$offset] = $value;
	}
	
	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset){
		unset($this->result[$offset]);
	}
}
