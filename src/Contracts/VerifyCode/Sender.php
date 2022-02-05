<?php

namespace Xin\Contracts\VerifyCode;

interface Sender
{
	/**
	 * 发送
	 * @param string $code
	 * @return bool
	 */
	public function send($identifier, $code);
}