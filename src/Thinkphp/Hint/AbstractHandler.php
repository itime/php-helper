<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\exception\HttpResponseException;
use Xin\Contracts\Hint\Handler;
use Xin\Hint\Hint;

abstract class AbstractHandler implements Handler {

	/**
	 * @var Hint
	 */
	protected $service;

	/**
	 * 设置提示器服务
	 * @param Hint $service
	 * @return void
	 */
	public function setHintService(Hint $service) {
		$this->service = $service;
	}

	/**
	 * @inheritDoc
	 */
	public function output($response, callable $callback = null) {
		if (is_callable($callback)) {
			call_user_func($callback, $response);
		}

		throw new HttpResponseException($response);
	}

	/**
	 * 优化数据
	 * @param mixed $data
	 * @return mixed
	 */
	public function optimizeData($data) {
		if (is_object($data)) {
			$data->time = request()->time();
		} else {
			$data['time'] = request()->time();
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function url($url) {
		return (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : (string)url($url ?: '');
	}

	/**
	 * 根据业务数据判断是否是成功行为
	 * @param mixed $data
	 * @return bool
	 */
	protected function isSuccess($data) {
		if (is_object($data)) {
			$code = $data->code;
		} else {
			$code = $data['code'];
		}

		return $code === 1;
	}

}
