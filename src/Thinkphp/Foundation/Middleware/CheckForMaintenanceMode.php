<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation\Middleware;

use think\App;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class CheckForMaintenanceMode{

	use InteractsExcept;

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
	 * The route operation to exclude is not authorized
	 *
	 * @var array
	 */
	protected $except = [];

	/**
	 * CheckForMaintenanceMode constructor.
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
		if($this->isDownForMaintenance()){
			// 允许正常访问的IP
			if(in_array($request->ip(), $this->getAllowIPs())){
				return $next($request);
			}

			// 允许正常访问的URL
			if($this->isExcept($request)){
				return $next($request);
			}

			throw new HttpResponseException(
				$this->createMaintenanceModeResponse($request)
			);
		}

		return $next($request);
	}

	/**
	 * 当前是否是维护模式
	 *
	 * @return bool
	 */
	protected function isDownForMaintenance(){
		return $this->config->get('web.site_close');
	}

	/**
	 * 允许访问的IP地址
	 *
	 * @return array
	 */
	protected function getAllowIPs(){
		return [];
	}

	/**
	 * 创建维护模式要返回的响应
	 *
	 * @param \think\Request $request
	 * @return \think\Response
	 * @throws \Exception
	 */
	protected function createMaintenanceModeResponse(Request $request){
		$closeMsg = $this->config->get('web.site_close_msg');
		$closeMsg = $closeMsg ? $closeMsg : '站点已关闭...';
		$response = $this->view->fetch($this->config->get('app.site_close_template'), [
			'msg' => $closeMsg,
		]);

		return Response::create($response);
	}
}
