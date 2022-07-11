<?php

namespace Xin\Contracts\VerifyCode;

interface Factory
{
	/**
	 * 使用短信验证码
	 * @return Repository
	 */
	public function sms();

	/**
	 * 使用邮箱验证码
	 * @return Repository
	 */
	public function email();

	/**
	 * 使用自定义验证码
	 * @param string $name
	 * @return Repository
	 */
	public function use($name = null);
}