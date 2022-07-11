<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Balance\Exceptions;

class BalanceModifyException extends \LogicException
{
	/**
	 * 默认Code吗
	 */
	public const DEFAULT_CODE = 10011;

	/**
	 * @param string $message
	 * @param int $code
	 * @param \Throwable|null $previous
	 */
	public function __construct($message = "Balance modify fail.", $code = self::DEFAULT_CODE, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
