<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Middleware;

use think\Response;

/**
 * Class AllowCrossDomain
 */
class AllowCrossDomain {

	/**
	 * 初始化应用
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next) {
		$httpOrigin = $this->httpOrigin($request);

		if ($request->isOptions()) {
			return $this->resolveResponse(
				$request,
				Response::create()->code(204),
				$httpOrigin
			);
		}

		return $this->resolveResponse(
			$request,
			$next($request),
			$httpOrigin
		);
	}

	/**
	 * 获取允许开放的来源地址
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function httpOrigin($request) {
		$httpOrigin = $request->header('origin');
		if (empty($httpOrigin)) {
			$httpOrigin = '*';
		}

		return $httpOrigin;
	}

	/**
	 * 跨域支持 header 数组
	 *
	 * @param \think\Request $request
	 * @return array
	 */
	protected function headers($request) {
		return [];
	}

	/**
	 * 响应内容增加跨域支持
	 *
	 * @param \think\Request $request
	 * @param Response       $response
	 * @param string         $httpOrigin
	 * @return mixed
	 */
	protected function resolveResponse($request, $response, $httpOrigin) {
		if (!$response instanceof Response) {
			return $response;
		}

		$header = array_merge([
			'Access-Control-Allow-Origin' => $httpOrigin,
			'Access-Control-Allow-Credentials' => 'true',
			'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
			'Access-Control-Allow-Headers' => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
			'Access-Control-Max-Age' => '3600',
		], $this->headers($request));

		return $response->header($header);
	}

}
