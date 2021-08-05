<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

if(!function_exists('auth')){
	/**
	 * @param string $guard
	 * @return object|\think\App|\Xin\Auth\AuthManager|\Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	function auth($guard = null){
		$auth = app('auth');
		if($guard){
			return $auth->guard($guard);
		}

		return $auth;
	}
}
