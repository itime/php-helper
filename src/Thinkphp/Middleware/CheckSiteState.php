<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Middleware;

use app\Request;
use app\View;
use think\Config;
use think\exception\HttpResponseException;
use think\Response;

class CheckSiteState{

	/**
	 * @var \app\Request
	 */
	private $request;

	/**
	 * @var \think\Config
	 */
	private $config;

	/**
	 * @var \app\View
	 */
	private $view;

	/**
	 * CheckSiteState constructor.
	 *
	 * @param \app\Request  $request
	 * @param \think\Config $config
	 * @param \app\View     $view
	 */
	public function __construct(Request $request, Config $config, View $view){
		$this->request = $request;
		$this->config = $config;
		$this->view = $view;
	}

	/**
	 * 初始化站点状态
	 *
	 * @param \app\Request $request
	 * @param \Closure     $next
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle($request, \Closure $next){
		if($this->config->get('web.site_close')
			&& !in_array($this->request->ip(), $this->getAllowIPs())){
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
