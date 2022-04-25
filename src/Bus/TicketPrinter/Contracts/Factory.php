<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\TicketPrinter\Contracts;

interface Factory
{

	/**
	 * 获取打印机
	 *
	 * @return \Xin\Bus\TicketPrinter\Contracts\Printer
	 */
	public function printer($driver);

}
