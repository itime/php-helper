<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation\Wechat;

use Throwable;

/**
 * @deprecated
 */
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
