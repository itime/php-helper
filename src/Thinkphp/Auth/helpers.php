<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

if (!function_exists('auth')) {
	/**
	 * 获取授权管理器
	 *
	 * @param string $guard
	 * @return object|\think\App|\Xin\Auth\AuthManager|\Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	function auth($guard = null) {
		$auth = app('auth');
		if ($guard) {
			return $auth->guard($guard);
		}

		return $auth;
	}
}

if (!function_exists('auth_user')) {
	/**
	 * 获取当前授权登录用户
	 *
	 * @param string $guard
	 * @return mixed
	 */
	function auth_user($guard = null) {
		return auth($guard)->user();
	}
}

if (!function_exists('auth_user_id')) {
	/**
	 * 获取当前授权登录用户ID
	 *
	 * @param string $guard
	 * @return int
	 */
	function auth_user_id($guard = null) {
		return auth($guard)->id();
	}
}

if (!function_exists('is_administrator')) {
	/**
	 * 检查当前登录的用户是否为超级管理员
	 *
	 * @param string $guard
	 * @return bool
	 */
	function is_administrator($guard = null) {
		return auth($guard)->isAdministrator();
	}
}

if (!function_exists('is_login')) {
	/**
	 * 检查是否已登录
	 *
	 * @param string $guard
	 * @return bool
	 */
	function is_login($guard = null) {
		return auth($guard)->check();
	}
}

if (!function_exists('is_guest')) {
	/**
	 * 检查当前是否是游客模式
	 *
	 * @param string $guard
	 * @return bool
	 */
	function is_guest($guard = null) {
		return auth($guard)->guest();
	}
}
