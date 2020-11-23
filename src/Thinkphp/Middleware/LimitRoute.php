<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Middleware;

use think\Request;
use Xin\Thinkphp\Foundation\InteractsPathinfo;

/**
 * Class LimitRoute
 *
 * @property array $except
 */
trait LimitRoute{
	
	use InteractsPathinfo;
	
	/**
	 * @param \think\Request $request
	 * @return bool
	 */
	protected function isExcept(Request $request){
		$except = property_exists($this, 'except') ? $this->except : [];
		return $this->pathIs($except);
	}
}
