<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

use app\common\model\XApp;
use app\Request;

trait InteractsAppApiMode{
	
	/**
	 * @param \Xin\Thinkphp\Http\RequestApp $request
	 */
	protected function saasAppInit($request){
		$request->setAppResolver(function(Request $request){
			$accessId = $request->get('access_id', '', 'trim');
			if(empty($accessId)){
				throw new \LogicException('access_id param invalid.');
			}
			
			return XApp::where('access_id', $accessId)->findOrFail();
		});
	}
}
