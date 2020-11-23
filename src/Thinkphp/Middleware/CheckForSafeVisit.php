<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use think\App;
use think\exception\HttpException;
use think\Request;

class CheckForSafeVisit{
	
	use LimitRoute;
	
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
	public function __construct(App $app){
		$this->app = $app;
		$this->config = $app['config'];
	}
	
	/**
	 * 检查站点是否允许访问
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle(Request $request, \Closure $next){
		$safeKey = $this->localSafeKey($request);
		
		if($safeKey != $this->resolveSafeKey($request)){
			// 要排除的URL
			if($this->isExcept($request)){
				return $next($request);
			}
			
			throw new HttpException(404, '页面不存在！');
		}
		
		return $next($request);
	}
	
	/**
	 * 获取本地的[safe_key]
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function localSafeKey(Request $request){
		return $this->config->get('app.safe_key');
	}
	
	/**
	 * 获取当前请求的[safe_key]
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function resolveSafeKey(Request $request){
		$safeKey = $request->cookie($this->cookieSafeKeyName());
		
		if(empty($safeKey)){
			$safeKey = $request->param($this->requestSafeKeyName(), '', 'trim');
			
			if($safeKey){
				$this->app->cookie->set($this->cookieSafeKeyName(), $safeKey);
			}
		}
		
		return $safeKey;
	}
	
	/**
	 * 获取 cookie 存储的[safe_key]名称
	 *
	 * @return string
	 */
	protected function cookieSafeKeyName(){
		return '__safe_key__';
	}
	
	/**
	 * 获取 当前请求的存储的[safe_key]参数名称
	 *
	 * @return string
	 */
	protected function requestSafeKeyName(){
		return '_safe_key';
	}
	
}
