<?php

namespace Xin\Contracts\VerifyCode;

interface Repository
{
	/**
	 * 生产验证码
	 * @param string $identifier
	 * @param string $type
	 * @param array $options
	 * @return string
	 */
	public function make($identifier, $type = null, $options = []);

	/**
	 * 验证验证码
	 * @param string $identifier
	 * @param string $code
	 * @param string $type
	 * @return string
	 */
	public function verify($identifier, $code, $type = null);

}