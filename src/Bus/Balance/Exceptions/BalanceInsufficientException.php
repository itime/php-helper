<?php

namespace Xin\Bus\Balance\Exceptions;

class BalanceInsufficientException extends \LogicException
{
	/**
	 * 默认Code吗
	 */
	public const DEFAULT_CODE = 10010;

	/**
	 * @param string $message
	 * @param int $code
	 * @param \Throwable|null $previous
	 */
	public function __construct($message = "Balance insufficient.", $code = self::DEFAULT_CODE, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}