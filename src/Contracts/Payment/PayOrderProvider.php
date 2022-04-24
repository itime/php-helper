<?php

namespace Xin\Contracts\Payment;

interface PayOrderProvider
{
	/**
	 * transactionId 转 业务订单号
	 * @param string $type
	 * @param string $transactionId
	 * @return string
	 */
	public function transactionIdToOutTradeNo($transactionId, $type = null);

	/**
	 * 根据系统订单号查找信息
	 * @param string $outTradeNo
	 * @param string $type
	 * @return mixed
	 */
	public function retrieveByOutTradeNo($outTradeNo, $type = null);

	/**
	 * 根据微信流水号查找信息
	 * @param string $transactionId
	 * @param string $type
	 * @return mixed
	 */
	public function retrieveByTransactionId($transactionId, $type = null);

	/**
	 * 根据支付订单号查找信息
	 * @param string $payTradeNo
	 * @param string $type
	 * @return mixed
	 */
	public function retrieveByPayTradeNo($payTradeNo, $type = null);

	/**
	 * 创建支付单
	 * @param array $data
	 * @return mixed
	 */
	public function createPayOrder($data);
}