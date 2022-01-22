<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Sms;

interface Channel {

	/**
	 * 发送短信
	 * @param string $phone
	 * @param array  $message
	 * @return mixed
	 */
	public function send($phone, $message);

}
