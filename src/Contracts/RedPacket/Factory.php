<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\RedPacket;

interface Factory{

	/**
	 * 生成红包金额记录
	 *
	 * @param array $options
	 * @return array
	 */
	public function generate(array $options);

	/**
	 * 红包生成器是否存在
	 *
	 * @param string $type
	 * @return bool
	 */
	public function hasGenerator($type);

	/**
	 * 领取红包
	 *
	 * @param array $options
	 * @return array
	 */
	public function receive(array $options);

	/**
	 * 红包领取器是否存在
	 *
	 * @param string $type
	 * @return bool
	 */
	public function hasReceiver($type);
}
