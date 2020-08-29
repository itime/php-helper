<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Facade;

use think\Facade;

/**
 * Class Hash
 * @method string make($value, array $options = []) static
 * @method bool check($value, $hashedValue, array $options = []) static
 * @method bool needsRehash($hashedValue, array $options = []) static
 *
 * @see \Xin\Support\Hasher
 */
class Hash extends Facade{
	
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass(){
		return 'hash';
	}
}
