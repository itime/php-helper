<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use Xin\Thinkphp\Saas\Model\DatabaseApp;

trait InteractsAppSessionMode{
	
	/**
	 * @param \Xin\Thinkphp\Saas\Http\RequestApp|\think\Request $request
	 */
	protected function saasAppInit($request){
		$request->setAppResolver(function($request){
			/** @var \Xin\Thinkphp\Saas\Http\RequestApp|\think\Request $request */
			
			$appId = $request->session('app_id');
			if(empty($appId)){
				$appId = $this->appIdStorageInvalid($request);
				if(empty($appId)){
					return null;
				}
			}
			
			return DatabaseApp::where('id', $appId)->findOrFail();
		});
	}
	
	/**
	 * AppId 失效时处理事件
	 *
	 * @param \think\Request $request
	 */
	protected function appIdStorageInvalid($request){
	}
}
