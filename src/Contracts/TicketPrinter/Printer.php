<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\TicketPrinter;

interface Printer
{

	/**
	 * 打印
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function send($data);

}
