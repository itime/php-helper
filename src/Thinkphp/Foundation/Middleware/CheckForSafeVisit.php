<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Middleware;

use think\App;
use think\exception\HttpException;
use think\Request;

class CheckForSafeVisit
{

	use InteractsExcept;

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Config
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $except = [];

	/**
	 * CheckForSafeVisit constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->config = $app['config'];
	}

	/**
	 * 检查站点是否允许访问
	 *
	 * @param \think\Request $request
	 * @param \Closure $next
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle(Request $request, \Closure $next)
	{
		$safeKey = $this->localSafeKey($request);

		if ($safeKey == $request->pathinfo()) {
			$this->app->cookie->set($this->cookieSafeKeyName(), $safeKey);
		} else {
			if ($safeKey != $this->clientSafeKey($request)) {
				// 要排除的URL
				if ($this->isExcept($request)) {
					return $next($request);
				}

				throw new HttpException(404, '页面不存在！');
			}
		}

		return $next($request);
	}

	/**
	 * 获取本地的[safe_key]
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function localSafeKey(Request $request)
	{
		return $this->config->get('app.safe_key');
	}

	/**
	 * 获取当前请求的[safe_key]
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function clientSafeKey(Request $request)
	{
		return $request->cookie($this->cookieSafeKeyName());
	}

	/**
	 * 获取 cookie 存储的[safe_key]名称
	 *
	 * @return string
	 */
	protected function cookieSafeKeyName()
	{
		return '__safe_key__';
	}

}
