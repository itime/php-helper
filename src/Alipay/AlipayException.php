<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Alipay;

class AlipayException extends \Exception
{

	/**
	 * 返回的结果
	 *
	 * @var mixed
	 */
	protected $response;

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 *
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param string $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param mixed $response
	 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
	 * @since 5.1.0
	 */
	public function __construct($message = "", $code = 0, $response = null, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->response = $response;
	}

	/**
	 * 获取返回的结果
	 *
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->response;
	}

}
