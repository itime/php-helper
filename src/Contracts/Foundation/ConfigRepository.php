<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface ConfigRepository extends \ArrayAccess{
	
	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has($key);
	
	/**
	 * 获取配置
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get($key, $default = null);
	
	/**
	 * 设置配置
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $isMerge
	 * @return mixed
	 */
	public function set($key, $value, $isMerge = true);
	
	/**
	 * 保存配置
	 *
	 * @return bool
	 */
	public function save();
	
	/**
	 * 获取所有的配置
	 *
	 * @return array
	 */
	public function all();
}
