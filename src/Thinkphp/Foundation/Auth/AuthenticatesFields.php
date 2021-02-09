<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

trait AuthenticatesFields{
	
	/**
	 * Get the auth username to be used by the controller.
	 *
	 * @return string
	 */
	protected function username(){
		return 'username';
	}
	
	/**
	 * Get the auth password to be used by the controller.
	 *
	 * @return string
	 */
	protected function password(){
		return 'password';
	}
	
	/**
	 * Get the auth password to be used by the controller.
	 *
	 * @return string
	 */
	protected function confirmPassword(){
		return 'confirm_password';
	}
}
