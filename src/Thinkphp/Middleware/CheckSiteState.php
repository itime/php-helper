<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use think\App;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class CheckSiteState{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var \think\Config
	 */
	protected $config;
	
	/**
	 * @var \think\View
	 */
	protected $view;
	
	/**
	 * CheckSiteState constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app){
		$this->config = $app['config'];
		$this->view = $app['view'];
	}
	
	/**
	 * 初始化站点状态
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle(Request $request, \Closure $next){
		if($this->config->get('web.site_close')
			&& !in_array($request->ip(), $this->getAllowIPs())){
			$closeMsg = $this->config->get('web.site_close_msg');
			$closeMsg = $closeMsg ? $closeMsg : '站点已关闭...';
			$response = $this->view->fetch('public/close', [
				'msg' => $closeMsg,
			]);
			
			$response = Response::create($response);
			throw new HttpResponseException($response);
		}
		
		return $next($request);
	}
	
	/**
	 * @return array
	 */
	protected function getAllowIPs(){
		return [];
	}
}
