<?php

namespace Xin\Contracts\VerifyCode;

interface Store
{
	/**
	 * 存储验证码
	 * @param string $type
	 * @param string $identifier
	 * @param string $code
	 * @return bool
	 */
	public function save($type, $identifier, $code, $seconds);

	/**
	 * 获取验证码
	 * @param string $type
	 * @param string $identifier
	 * @return string
	 */
	public function get($type, $identifier);

	/**
	 * 清除验证码
	 * @param string $type
	 * @param string $identifier
	 * @return bool
	 */
	public function forget($type, $identifier);
}