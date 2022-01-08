<?php

namespace Xin\Wechat;

use Xin\Wechat\Exceptions\WechatBusinessException;
use Xin\Wechat\Exceptions\WechatException;

class WechatErrorHandle {

	/**
	 * @param int    $errCode
	 * @param string $errMsg
	 * @param mixed  $result
	 * @return WechatBusinessException
	 */
	public function handle($errCode, $errMsg, $result) {
		return new WechatBusinessException($errMsg, $errCode);
	}

	/**
	 * 异常处理方法
	 * @param \Exception $exception
	 * @return WechatException
	 */
	public function handleException(\Exception $exception) {
		return new WechatException($exception->getMessage(), $exception->getCode(), $exception);
	}

}
