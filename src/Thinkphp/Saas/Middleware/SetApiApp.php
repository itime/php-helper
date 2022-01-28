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
use Xin\Thinkphp\Saas\Payment\PaymentServiceProvider;
use Xin\Thinkphp\Saas\Wechat\WechatServiceProvider;

class SetApiApp
{

	/**
	 * @var \Xin\Thinkphp\Http\HasApp
	 */
	protected $request;

	/**
	 * api模式下检查当前应用是否合法
	 *
	 * @param \Xin\Thinkphp\Http\HasApp $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next)
	{
		$this->request = $request;

		$request->setAppResolver(function (Request $request) {
			$accessId = $request->get('access_id', '', 'trim');
			if (empty($accessId)) {
				throw new \LogicException('access_id param invalid.');
			}

			return $this->resolve($accessId);
		});

		// 微信实例器绑定AppId
		$this->wechatBindAppId();

		// 支付器绑定AppId
		$this->paymentBindAppId();

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
	protected function resolve($accessId)
	{
		$xApp = DatabaseApp::where('access_id', $accessId)->find();
		if (!$xApp) {
			throw AppNotFoundException::ofAccessId($accessId);
		}

		return $xApp;
	}

	/**
	 * 微信实例器绑定AppId
	 * @return void
	 */
	protected function wechatBindAppId()
	{
		WechatServiceProvider::bindAppId($this->request->appId());
	}

	/**
	 * 支付器绑定AppId
	 * @return void
	 */
	protected function paymentBindAppId()
	{
		PaymentServiceProvider::bindAppId($this->request->appId());
	}


}
