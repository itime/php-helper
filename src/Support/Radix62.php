<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Support;

/**
 * 62位进制转换器
 * @method static string generate(int $num)
 * @method static int parse(string $str)
 */
class Radix62{
	
	/**
	 * @return IDGenerator
	 */
	protected static function idGeneratorInstance(){
		static $instance = null;
		
		if($instance === null){
			$instance = new IDGenerator(
				'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
			);
		}
		
		return $instance;
	}
	
	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments){
		return call_user_func_array([
			self::idGeneratorInstance(), $name,
		], $arguments);
	}
}
