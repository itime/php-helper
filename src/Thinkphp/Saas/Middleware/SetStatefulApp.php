<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use think\exception\HttpResponseException;
use think\facade\Session;
use think\Response;
use Xin\Thinkphp\Facade\Auth;
use Xin\Thinkphp\Saas\DatabaseApp;

class SetStatefulApp{

	/**
	 * api模式下检查当前应用是否合法
	 *
	 * @param \think\Request|\Xin\Thinkphp\Http\HasApp $request
	 * @param \Closure                                 $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next){
		$request->setAppResolver(function(){
			$xApp = Session::get('app');

			if(!$xApp){
				$userId = Auth::id();
				$xApp = $this->resolve($userId);
				Session::set('app', $xApp);
			}

			return $xApp;
		});

		return $next($request);
	}

	/**
	 * @param int $userId
	 * @return array|\think\Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function resolve($userId){
		$xApp = DatabaseApp::where(['store_id' => $userId])->find();

		if(!$xApp){
			throw new HttpResponseException(
				Response::create(url('app/create'), 'redirect')
			);
		}

		return $xApp;
	}
}
