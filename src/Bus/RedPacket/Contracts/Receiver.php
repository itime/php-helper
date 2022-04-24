<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\RedPacket\Contracts;

interface Receiver
{

	/**
	 * 领取红包
	 *
	 * @return array
	 */
	public function receive();

}
