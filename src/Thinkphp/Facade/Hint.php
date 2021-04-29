<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Facade;

use think\Facade;

/**
 * @method \Xin\Hint\HintManager shouldUseApi() static
 * @method \Xin\Hint\HintManager shouldUseWeb() static
 * @method \think\Response success($msg, $url = null, $data = null, array $extend = []) static
 * @method \think\Response error($msg, $code = 0, $url = null, array $extend = []) static
 * @method \think\Response alert($msg, $code = 0, $url = null, array $extend = []) static
 * @method \think\Response result($data = [], array $extend = []) static
 * @method \think\Response outputSuccess($msg, $url = null, $data = null, array $extend = [], callable $callback = null) static
 * @method \think\Response outputError($msg, $code = 0, $url = null, array $extend = [], callable $callback = null) static
 * @method \think\Response outputAlert($msg, $code = 0, $url = null, array $extend = [], callable $callback = null) static
 * @see \Xin\Contracts\Hint\Factory
 * @see \Xin\Contracts\Hint\Hint
 */
class Hint extends Facade{

	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass(){
		return 'hint';
	}
}
