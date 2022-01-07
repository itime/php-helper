<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Sms;

interface Message {

	/**
	 * 获取内容
	 *
	 * @return string
	 */
	public function getContent();

	/**
	 * 获取数据
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * 获取模板
	 *
	 * @return string
	 */
	public function template();

}
