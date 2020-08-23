<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\Request;

trait TokenGuardHelpers{
	
	/**
	 * @var \Closure
	 */
	protected $authTokenResolver;
	
	/**
	 * @return string
	 */
	public function getAuthToken(){
		return call_user_func($this->authTokenResolver, $this->request, $this);
	}
	
	/**
	 * @return \Closure
	 */
	protected function getDefaultAuthTokenResolver(){
		return function(Request $request){
			return $request->param('session_id');
		};
	}
}
