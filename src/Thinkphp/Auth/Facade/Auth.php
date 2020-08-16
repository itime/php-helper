<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Auth\Facade;

use think\Facade;

/**
 * Class Auth
 * @method \Xin\Contracts\Auth\Guard guard($name = null) static
 * @method void shouldUse($name = null) static
 *
 * @mixin \Xin\Auth\AuthManager
 */
class Auth extends Facade{
	
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass(){
		return 'auth';
	}
	
}
