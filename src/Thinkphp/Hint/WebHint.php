<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\facade\Config;
use think\Response;
use Xin\Contracts\Hint\Hint as HintContract;

class WebHint implements HintContract{

	use HintHelper;

	/**
	 * @inheritDoc
	 */
	public function result($data = [], array $extend = []){
		return $this->success('操作成功！', null, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function success($msg = '', $url = null, $data = null, array $extend = []){
		$url = $this->resolveSuccessUrl($url);

		$result = array_merge([
			'code' => 1,
			'msg'  => $msg,
			'data' => $data,
			'url'  => $url,
			'wait' => 1,
		], $extend);

		return $this->resolve(true, $result);
	}

	/**
	 * @inheritDoc
	 */
	public function error($msg, $code = 0, $url = null, array $extend = []){
		$url = $this->resolveUrl($url);

		$result = array_merge([
			'code' => 0,
			'msg'  => $msg,
			'data' => null,
			'url'  => $url,
			'wait' => 3,
		], $extend);

		return $this->resolve(false, $result);
	}

	/**
	 * make Response
	 *
	 * @param bool  $isSuccess
	 * @param array $result
	 * @return \think\response\View
	 */
	protected function resolve($isSuccess, array $result){
		$configPrefix = empty(Config::get('jump.')) ? 'app' : 'jump';
		if($isSuccess){
			$data = Config::get($configPrefix.'.dispatch_success_tmpl');
		}else{
			$data = Config::get($configPrefix.'.dispatch_error_tmpl');
		}

		/** @var \think\response\View $response */
		$response = Response::create($data, 'view');
		$response->assign($result);

		return $response;
	}

}
