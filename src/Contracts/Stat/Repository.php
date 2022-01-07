<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Stat;

interface Repository {

	/**
	 * 自定义统计
	 *
	 * @param string $name
	 * @param int    $step
	 * @param array  $options
	 */
	public function tally($name, $step = 1, array $options = []);

	/**
	 * 统计IP
	 *
	 * @param array $options
	 */
	public function tallyIP(array $options = []);

	/**
	 * 获取统计的值
	 *
	 * @param string $name
	 * @param int    $time
	 * @param array  $options
	 * @return int
	 */
	public function value($name, $time = null, array $options = []);

	/**
	 * 获取统计总值
	 *
	 * @param string $name
	 * @param array  $options
	 * @return int
	 */
	public function total($name, array $options = []);

}
