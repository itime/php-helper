<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use Xin\Support\Fluent;

class SetDefaultApp
{

	/**
	 * api模式下检查当前应用是否合法
	 *
	 * @param \Xin\Thinkphp\Http\HasApp $request
	 * @param \Closure $next
	 * @param int $appId
	 * @return mixed
	 */
	public function handle($request, \Closure $next, $appId = 0)
	{
		$request->setAppResolver(function () use ($appId) {
			return new Fluent([
				'id' => intval($appId),
				'title' => '默认应用',
			]);
		});

		return $next($request);
	}

}
