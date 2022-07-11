<?php

namespace Xin\Wechat;

use Exception;
use Psr\Http\Message\StreamInterface;
use Throwable;
use Xin\Support\Arr;
use Xin\Wechat\Exceptions\WechatBusinessException;
use Xin\Wechat\Exceptions\WechatException;

class WechatResult implements \ArrayAccess
{

	/**
	 * @var mixed
	 */
	protected $result;

	/**
	 * @var Throwable
	 */
	protected $exception;

	/**
	 * @var Exception
	 */
	protected $businessException;

	/**
	 * @var callable
	 */
	protected static $exceptionResolver = [WechatErrorHandle::class, 'handleException'];

	/**
	 * @var callable
	 */
	protected static $errorResolver = [WechatErrorHandle::class, 'handle'];

	/**
	 * @param mixed $result
	 * @param Throwable|null $exception
	 */
	protected function __construct($result, Throwable $exception = null)
	{
		$this->result = $result;
		$this->exception = $exception;
	}

	/**
	 * 获取异常信息
	 * @return Throwable
	 */
	public function exception()
	{
		return $this->exception;
	}

	/**
	 * 获取错误码
	 * @return int
	 */
	public function errCode()
	{
		if (!$this->result) {
			return null;
		}

		return $this->isStream() ? 0 : (int)($this->result['errcode'] ?? 0);
	}

	/**
	 * 获取错误信息
	 * @return string
	 */
	public function errMessage()
	{
		if (!$this->result) {
			return null;
		}

		return $this->result['errmsg'] ?? '';
	}

	/**
	 * 业务是否成功
	 * @return bool
	 */
	public function isSucceeded()
	{
		return $this->errCode() === 0;
	}

	/**
	 * @return bool
	 */
	public function isAccessError()
	{
		return $this->errCode() !== 60011;
	}

	/**
	 * 检查并抛出异常
	 * @return $this
	 * @throws WechatException
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function throw()
	{
		if ($this->exception) {
			throw $this->resolveException($this->exception);
		}

		if (!$this->isSucceeded()) {
			$businessException = $this->resolveBusinessException();
			if ($businessException) {
				throw $businessException;
			}
		}

		return $this;
	}

	/**
	 * 解析异常
	 * @param Throwable $exception
	 * @return Throwable|WechatException
	 */
	protected function resolveException(Throwable $exception)
	{
		if (self::$exceptionResolver) {
			return call_user_func(self::$exceptionResolver, $exception);
		}

		return $exception;
	}

	/**
	 * 解析逻辑异常
	 * @return false|mixed|WechatBusinessException
	 */
	protected function resolveBusinessException()
	{
		if (self::$errorResolver) {
			return call_user_func(self::$errorResolver, $this->errCode(), $this->errMessage(), $this->result);
		}

		return new WechatBusinessException($this->errMessage(), $this->errCode());
	}

	/**
	 * 监听业务错误事件
	 *
	 * @param int|array $errCode
	 * @param callable $callback
	 * @return $this
	 */
	public function error($checkCodes, callable $callback)
	{
		$checkCodes = is_array($checkCodes) ? $checkCodes : [$checkCodes];

		if (in_array($this->errCode(), $checkCodes)) {
			$callback($this);
		}

		return $this;
	}

	/**
	 * 业务成功
	 * @param callable $callback
	 * @return $this
	 */
	public function then(callable $callback)
	{
		if ($this->isSucceeded()) {
			$callback($this->result);
		}

		return $this;
	}

	/**
	 * 业务失败
	 * @param callable $callback
	 * @return $this
	 * @throws WechatException
	 */
	public function catch(callable $callback)
	{
		if ($this->isSucceeded()) {
			return $this;
		}

		$result = $callback($this);
		if ($result !== null) {
			return $this;
		}

		$this->throw();

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return Arr::except($this->result, [
			'errcode', 'errmsg',
		]);
	}

	/**
	 * @return mixed
	 */
	public function getRaw()
	{
		return $this->result;
	}

	/**
	 * @return bool
	 */
	public function isStream()
	{
		return $this->result instanceof StreamInterface;
	}

	/**
	 * Whether a offset exists
	 * @link https://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return bool true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return isset($this->result[$offset]);
	}

	/**
	 * Offset to retrieve
	 * @link https://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		return $this->result[$offset];
	}

	/**
	 * Offset to set
	 * @link https://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
	}

	/**
	 * Offset to unset
	 * @link https://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset)
	{
	}

	/**
	 * 设置异常处理器
	 * @param callable $exceptionResolver
	 */
	public static function setExceptionResolver(callable $exceptionResolver)
	{
		self::$exceptionResolver = $exceptionResolver;
	}

	/**
	 * 设置业务错误处理器
	 * @param callable $errorResolver
	 */
	public static function setErrorResolver(callable $errorResolver): void
	{
		self::$errorResolver = $errorResolver;
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public static function capture(callable $callback)
	{
		$exception = null;
		$result = null;

		try {
			$result = $callback();
		} catch (Throwable $throwable) {
			$exception = $throwable;
		}

		return static::make($result, $exception);
	}

	/**
	 * @param mixed $result
	 * @param Throwable|null $exception
	 * @return static
	 */
	public static function make($result, Throwable $exception = null)
	{
		return new static($result, $exception);
	}

}
