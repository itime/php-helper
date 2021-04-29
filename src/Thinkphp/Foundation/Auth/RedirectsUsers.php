<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

trait RedirectsUsers{

	/**
	 * Get the post register / login redirect path.
	 *
	 * @return string
	 */
	protected function redirectPath(){
		if(method_exists($this, 'redirectTo')){
			return $this->redirectTo();
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : (string)url('index/index');
	}
}
