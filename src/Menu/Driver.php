<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Contracts\Menu\Writer;
use Xin\Support\Arr;

abstract class Driver implements Writer{
	
	/**
	 * @var $config
	 */
	protected $config;
	
	/**
	 * Driver constructor.
	 *
	 * @param $config
	 */
	public function __construct($config){
		$this->config = $config;
	}
	
	/**
	 * 获取配置
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return array|\ArrayAccess|mixed
	 */
	public function config($key, $default = null){
		if(empty($key)){
			return $this->config;
		}
		
		return Arr::get($this->config, $key, $default);
	}
	
	/**
	 * 遍历菜单
	 *
	 * @param string $callback
	 * @param array  $menus
	 * @param mixed  $parent
	 */
	protected static function each($callback, &$menus, &$parent = null){
		foreach($menus as $key => &$menu){
			$result = call_user_func_array($callback, [&$menu, &$parent]);
			
			if(isset($menu['child'])){
				self::each($callback, $menu['child'], $result);
			}
		}
		unset($menu);
	}
}
