<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Cache\Driver;

use think\cache\Driver;

class L2Cache extends Driver{
	
	public function inc(string $name, int $step = 1){
		// TODO: Implement inc() method.
	}
	
	public function dec(string $name, int $step = 1){
		// TODO: Implement dec() method.
	}
	
	public function clearTag(array $keys){
		// TODO: Implement clearTag() method.
	}
	
	public function get($key, $default = null){
		// TODO: Implement get() method.
	}
	
	public function set($key, $value, $ttl = null){
		// TODO: Implement set() method.
	}
	
	public function delete($key){
		// TODO: Implement delete() method.
	}
	
	public function clear(){
		// TODO: Implement clear() method.
	}
	
	public function has($key){
		// TODO: Implement has() method.
	}
}
