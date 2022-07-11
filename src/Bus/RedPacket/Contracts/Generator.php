<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\RedPacket\Contracts;

interface Generator
{

	/**
	 * 红包金额生成
	 *
	 * @return array|float
	 */
	public function generate();

}
