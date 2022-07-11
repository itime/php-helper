<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Sms;

interface Factory
{

	/**
	 * @param string $name
	 * @return Channel
	 */
	public function channel($name = null);

}
