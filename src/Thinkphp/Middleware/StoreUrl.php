<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use think\Cookie;
use think\Request;

class StoreUrl{

	/**
	 * @var \think\Cookie
	 */
	private $cookie;

	/**
	 * StoreUrl constructor.
	 *
	 * @param \think\Cookie $cookie
	 */
	public function __construct(Cookie $cookie){
		$this->cookie = $cookie;
	}

	/**
	 * @param \think\Request|\Xin\Thinkphp\Http\Requestable $request
	 * @param \Closure                                      $next
	 * @return mixed
	 */
	public function handle(Request $request, \Closure $next){
		if($request->method() === 'GET' &&
			!$request->isAjax() &&
			!$request->prefetch()){
			$this->storeUrl($request);
		}

		return $next($request);
	}

	/**
	 * @param \think\Request|\Xin\Thinkphp\Http\Requestable $request
	 */
	private function storeUrl($request){
		$previousUrl = $this->cookie->get('_previous_url');
		$currentUrl = $this->cookie->get('_current_url');

		$requestUrl = $request->fullUrl();

		if(empty($currentUrl)){
			$currentUrl = $requestUrl;
		}

		if(!$previousUrl || $currentUrl != $requestUrl){
			$this->cookie->set('_previous_url', $currentUrl);
		}

		$this->cookie->set('_current_url', $request->fullUrl());
	}
}
