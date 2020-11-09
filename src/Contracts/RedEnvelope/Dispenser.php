<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\RedEnvelope;

interface Dispenser{
	
	/**
	 * 领取红包
	 *
	 * @return bool
	 */
	public function giveOut();
}
