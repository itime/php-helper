<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Middleware;

use Xin\Auth\AuthenticationException;
use Xin\Contracts\Auth\Factory as Auth;

class Authenticate{
	
	/**
	 * The authentication factory instance.
	 *
	 * @var \Xin\Contracts\Auth\Factory
	 */
	protected $auth;
	
	/**
	 * Create a new middleware instance.
	 *
	 * @param \Xin\Contracts\Auth\Factory $auth
	 * @return void
	 */
	public function __construct(Auth $auth){
		$this->auth = $auth;
	}
	
	/**
	 * Handle an incoming request.
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @param string         ...$guards
	 * @return mixed
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function handle($request, \Closure $next, ...$guards){
		$this->authenticate($request, $guards);
		
		return $next($request);
	}
	
	/**
	 * Determine if the user is logged in to any of the given guards.
	 *
	 * @param \think\Request $request
	 * @param array          $guards
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 * @throws \Xin\Auth\AuthenticationException
	 */
	protected function authenticate($request, array $guards){
		if(empty($guards)){
			$guards = [null];
		}
		
		foreach($guards as $guard){
			if($this->auth->guard($guard)->check()){
				return $this->auth->shouldUse($guard);
			}
		}
		
		throw new AuthenticationException(
			$guards, $this->auth->guard()->getConfig()
		);
	}
}
