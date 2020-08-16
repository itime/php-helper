<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use think\Response;

/**
 * Class AllowCrossDomain
 */
class AllowCrossDomain{

	/**
	 * 初始化应用
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next){
		if($request->isOptions()){
			$httpOrigin = $request->header('origin');
			if(empty($httpOrigin)){
				$httpOrigin = '*';
			}

			$header = [
				'Access-Control-Allow-Origin'      => $httpOrigin,
				'Access-Control-Allow-Credentials' => 'true',
				'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE',
				'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
			];

			return Response::create()->code(204)->header($header);
		}

		return $next($request);
	}
}
