<?php

namespace Xin\Contracts\Payment;

interface Gateway
{
	/**
	 * 小程序支付
	 *
	 * @param array $paymentInfo
	 * @return \Yansongda\Supports\Collection
	 */
	public function miniapp(array $paymentInfo);

	/**
	 * App 支付
	 * @param array $paymentInfo
	 * @return \Yansongda\Supports\Collection
	 */
	public function app(array $paymentInfo);
}