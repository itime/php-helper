<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use app\Request;
use Xin\Thinkphp\Saas\Model\DatabaseApp;

trait InteractsAppApiMode{
	
	/**
	 * @param \Xin\Thinkphp\Saas\Http\RequestApp $request
	 */
	protected function saasAppInit($request){
		$request->setAppResolver(function(Request $request){
			$accessId = $request->get('access_id', '', 'trim');
			if(empty($accessId)){
				throw new \LogicException('access_id param invalid.');
			}
			
			return DatabaseApp::where('access_id', $accessId)->findOrFail();
		});
	}
}
