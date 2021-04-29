<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Stat;

interface StatProvider{

	/**
	 * 获取缓存 key
	 *
	 * @param string $name
	 * @param array  $options
	 * @return string
	 */
	public function getCacheKey($name, array $options = []);

	/**
	 * 根据时间获取统计ID
	 *
	 * @param string $name
	 * @param int    $time
	 * @param array  $options
	 * @return mixed
	 */
	public function getIdByTime($name, $time, array $options = []);

	/**
	 * 根据时间获取统计的值
	 *
	 * @param string $name
	 * @param int    $time
	 * @param array  $options
	 * @return int
	 */
	public function getValueByTime($name, $time = null, array $options = []);

	/**
	 * 根据ID获取统计的值
	 *
	 * @param int   $id
	 * @param array $options
	 * @return int
	 */
	public function getValueById($id, array $options = []);

	/**
	 * 获取统计总值
	 *
	 * @param string $name
	 * @param array  $options
	 * @return int
	 */
	public function getTotal($name, array $options = []);

	/**
	 * 插入数据
	 *
	 * @param array $data
	 * @param array $options
	 * @return int
	 */
	public function insert($data, array $options = []);

	/**
	 * 更新计数器
	 *
	 * @param int   $id
	 * @param int   $step
	 * @param array $options
	 * @return int
	 */
	public function incById($id, $step = 1, array $options = []);

	/**
	 * 根据实际获取统计IP数据
	 *
	 * @param string $ip
	 * @param int    $time
	 * @param array  $options
	 * @return int
	 */
	public function getIPIdByTime($ip, $time, array $options = []);

	/**
	 * 插入IP访问记录数据
	 *
	 * @param array $data
	 * @param array $options
	 * @return int
	 */
	public function insertIpLog($data, array $options = []);
}
