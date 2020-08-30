<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\exception\ValidateException;
use think\Request;
use Xin\Auth\Events\Lockout;
use Xin\Support\Str;
use Xin\Thinkphp\Cache\RateLimiter;

trait ThrottlesLogins{
	
	/**
	 * Determine if the user has too many failed login attempts.
	 *
	 * @param Request $request
	 * @return bool
	 */
	protected function hasTooManyLoginAttempts(Request $request){
		return $this->limiter()->tooManyAttempts(
			$this->throttleKey($request), $this->maxAttempts()
		);
	}
	
	/**
	 * Increment the login attempts for the user.
	 *
	 * @param Request $request
	 * @return void
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function incrementLoginAttempts(Request $request){
		$this->limiter()->hit(
			$this->throttleKey($request), $this->decayMinutes() * 60
		);
	}
	
	/**
	 * Redirect the user after determining they are locked out.
	 *
	 * @param Request $request
	 * @return void
	 */
	protected function sendLockoutResponse(Request $request){
		$seconds = $this->limiter()->availableIn(
			$this->throttleKey($request)
		);
		
		throw new ValidateException(
			"登录尝试过多，请在 $seconds 秒后重试~"
		);
	}
	
	/**
	 * Clear the login locks for the given user credentials.
	 *
	 * @param Request $request
	 * @return void
	 */
	protected function clearLoginAttempts(Request $request){
		$this->limiter()->clear($this->throttleKey($request));
	}
	
	/**
	 * Fire an event when a lockout occurs.
	 *
	 * @param Request $request
	 * @return void
	 */
	protected function fireLockoutEvent(Request $request){
		event(new Lockout($request));
	}
	
	/**
	 * Get the throttle key for the given request.
	 *
	 * @param Request $request
	 * @return string
	 */
	protected function throttleKey(Request $request){
		return Str::lower($request->input($this->username())).'|'.$request->ip();
	}
	
	/**
	 * Get the rate limiter instance.
	 *
	 * @return object|\think\App|\Xin\Thinkphp\Cache\RateLimiter
	 */
	protected function limiter(){
		return app(RateLimiter::class);
	}
	
	/**
	 * Get the maximum number of attempts to allow.
	 *
	 * @return int
	 */
	public function maxAttempts(){
		return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
	}
	
	/**
	 * Get the number of minutes to throttle for.
	 *
	 * @return int
	 */
	public function decayMinutes(){
		return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
	}
}
