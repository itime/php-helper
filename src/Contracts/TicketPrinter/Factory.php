<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\TicketPrinter;

interface Factory {

	/**
	 * 获取打印机
	 *
	 * @return \Xin\Contracts\TicketPrinter\Printer
	 */
	public function printer($driver);

}
