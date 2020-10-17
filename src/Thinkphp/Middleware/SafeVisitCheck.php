<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use think\App;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Request;

class SafeVisitCheck{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var \think\Config
	 */
	protected $config;
	
	/**
	 * CheckSiteState constructor.
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
		$safeKey = $this->safeKey($request);
		
		if($safeKey != $this->resolveSafeKey($request)){
			throw new HttpException(404, '页面不存在！');
		}
		
		return $next($request);
	}
	
	/**
	 * 获取本地的 safe_key
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function safeKey(Request $request){
		return $this->config->get('app.safe_key');
	}
	
	/**
	 * 解析 safe_key
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function resolveSafeKey(Request $request){
		$safeKey = $request->cookie($this->safeKeyKey());
		if(empty($safeKey)){
			$safeKey = $request->param('_safe_key', '', 'trim');
		
			if($safeKey){
				$this->app->cookie->set($this->safeKeyKey(), $safeKey);
			}
		}
		
		return $safeKey;
	}
	
	/**
	 * store safe_key key
	 *
	 * @return string
	 */
	protected function safeKeyKey(){
		return '__safe_key__';
	}
	
}
