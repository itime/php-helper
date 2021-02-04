<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface Payment{
	
	/**
	 * 微信支付
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function wechat(array $options = []);
	
	/**
	 * 支付宝支付
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function alipay(array $options = []);
}
