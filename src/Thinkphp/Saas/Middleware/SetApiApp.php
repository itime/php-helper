<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use app\Request;
use Xin\Thinkphp\Saas\App\DatabaseApp;
use Xin\Thinkphp\Saas\Exceptions\AppNotFoundException;

class SetApiApp {

	/**
	 * api模式下检查当前应用是否合法
	 *
	 * @param \Xin\Thinkphp\Http\HasApp $request
	 * @param \Closure                  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next) {
		$request->setAppResolver(function (Request $request) {
			$accessId = $request->get('access_id', '', 'trim');
			if (empty($accessId)) {
				throw new \LogicException('access_id param invalid.');
			}

			return $this->resolve($accessId);
		});

		return $next($request);
	}

	/**
	 * 解析应用信息
	 *
	 * @param string $accessId
	 * @return array|\think\Model|\Xin\Thinkphp\Saas\App\DatabaseApp
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function resolve($accessId) {
		$xApp = DatabaseApp::where('access_id', $accessId)->find();
		if (!$xApp) {
			throw AppNotFoundException::ofAccessId($accessId);
		}

		return $xApp;
	}

}
