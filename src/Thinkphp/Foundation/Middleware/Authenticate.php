<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Middleware;

use think\App;
use Xin\Auth\AuthenticationException;
use Xin\Contracts\Auth\Factory as Auth;

class Authenticate
{

	use InteractsExcept;

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * The authentication factory instance.
	 *
	 * @var \Xin\Contracts\Auth\Factory
	 */
	protected $auth;

	/**
	 * The route operation to exclude is not authorized
	 *
	 * @var array
	 */
	protected $except = [];

	/**
	 * Create a new middleware instance.
	 *
	 * @param \think\App $app
	 * @param \Xin\Contracts\Auth\Factory $auth
	 */
	public function __construct(App $app, Auth $auth)
	{
		$this->app = $app;
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \think\Request $request
	 * @param \Closure $next
	 * @param string ...$guards
	 * @return mixed
	 * @throws \Xin\Auth\AuthenticationException
	 */
	public function handle($request, \Closure $next, ...$guards)
	{
		$this->authenticate($request, $guards);

		return $next($request);
	}

	/**
	 * Determine if the user is logged in to any of the given guards.
	 *
	 * @param \think\Request $request
	 * @param array $guards
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 * @throws \Xin\Auth\AuthenticationException
	 */
	protected function authenticate($request, array $guards)
	{
		if (empty($guards)) {
			$guards = [null];
		}

		foreach ($guards as $guard) {
			if ($this->auth->guard($guard)->check()) {
				return $this->auth->shouldUse($guard);
			}
		}

		if ($this->isExcept($request) === true) {
			return null;
		}

		$config = $this->auth->guard()->getConfig();
		throw new AuthenticationException(
			$guards[0],
			$this->redirectTo($request),
			$config
		);
	}

	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param \think\Request $request
	 * @return string|void
	 */
	protected function redirectTo($request)
	{
		if ($request->isJson() || $request->isAjax()) {
			return;
		}
	}

}
