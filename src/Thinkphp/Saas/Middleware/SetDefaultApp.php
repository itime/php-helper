<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use Xin\Support\Fluent;

class SetDefaultApp{

	/**
	 * api模式下检查当前应用是否合法
	 *
	 * @param \Xin\Thinkphp\Http\HasApp $request
	 * @param \Closure                  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next){
		$request->setAppResolver(function(){
			return new Fluent([
				'id'    => 1,
				'title' => '默认应用',
			]);
		});

		return $next($request);
	}
}
