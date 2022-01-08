<?php

namespace Xin\Wechat\Exceptions;

use Throwable;

class WechatInvalidConfigException extends WechatException {

	/**
	 * WechatInvalidConfigException constructor.
	 *
	 * @param string          $message
	 * @param int             $code
	 * @param \Throwable|null $previous
	 */
	public function __construct($message = "", $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}

}
