<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Middleware;

use think\Request;

/**
 * @property array $except
 */
trait InteractsExcept{
	
	/**
	 * @param \Xin\Thinkphp\Http\Requestable $request
	 * @return bool
	 */
	protected function isExcept(Request $request){
		$except = property_exists($this, 'except') ? $this->except : [];
		return $request->pathIs($except);
	}
}
