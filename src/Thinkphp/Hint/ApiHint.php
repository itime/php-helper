<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\Request;
use think\Response;
use Xin\Contracts\Hint\Hint as HintContract;

class ApiHint implements HintContract {

	use HintHelper;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * ApiHint constructor.
	 *
	 * @param \think\Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}

	/**
	 * @inheritDoc
	 */
	public function result($data = [], array $extend = []) {
		return $this->success('OK', null, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function success($msg, $url = null, $data = null, array $extend = []) {
		if ($url) {
			$url = $this->resolveSuccessUrl($url);
			$extend['url'] = $url;
		}

		return $this->resolve(1, $msg, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function error($msg, $code = 0, $url = null, array $extend = []) {
		if ($msg instanceof \Exception) {
			$code = $msg->getCode();
			$msg = $msg->getMessage();
			$extend = is_array($code) ? $code : [];
		}

		return $this->resolve($code, $msg, null, $extend);
	}

	/**
	 * make Response
	 *
	 * @param string $code
	 * @param string $msg
	 * @param mixed  $data
	 * @param array  $extend
	 * @return \think\response
	 */
	protected function resolve($code, $msg, $data, array $extend = []) {
		return Response::create(array_merge([
			'code' => $code,
			'msg' => $msg,
			'data' => $data,
			'time' => request()->time(),
		], $extend), 'json');
	}

}
