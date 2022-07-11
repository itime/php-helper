<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use app\Request;
use think\App;
use think\exception\HttpException;

abstract class CheckForPluginAccess
{

	/**
	 * @var \think\App
	 */
	private $app;

	/**
	 * @var array
	 */
	protected $exclude = [];

	/**
	 * CheckForPluginAccess constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * @param \app\Request $request
	 * @param callable $next
	 * @return \think\response\View
	 */
	public function handle(Request $request, $next)
	{
		$this->app->middleware->add(function ($request, \Closure $next) {
			if (!$this->hasPluginAccess($request)) {
				$e = new HttpException(403, '无此权限！');
				if ($request->isAjax()) {
					throw $e;
				}

				return $this->notAccessView($e);
			}

			return $next($request);
		}, 'controller');

		return $next($request);
	}

	/**
	 * 当前请求是否拥护插件权限
	 *
	 * @param \app\Request $request
	 * @return bool
	 */
	protected function hasPluginAccess(Request $request)
	{
		$plugin = $request->plugin();
		if (empty($plugin) || in_array($plugin, $this->exclude)) {
			return true;
		}

		$xApp = $request->app();
		unset($xApp->plugins);
		$plugins = $request->app()->plugins->column('name');

		return in_array($plugin, $plugins);
	}

	/**
	 * 输出没有权限视图
	 *
	 * @return \think\Response
	 */
	abstract protected function notAccessView(HttpException $e);

}
