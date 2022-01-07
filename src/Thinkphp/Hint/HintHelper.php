<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\exception\HttpResponseException;

/**
 * Trait OutputHint
 *
 * @mixin \Xin\Contracts\Hint\Hint
 */
trait HintHelper {

	/**
	 * @param string $url
	 * @return mixed|string
	 */
	protected function resolveSuccessUrl($url) {
		if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
			$url = $_SERVER["HTTP_REFERER"];
		} elseif ($url) {
			$url = $this->resolveUrl($url);
		}

		return $url;
	}

	/**
	 * @param string $url
	 * @return mixed|string
	 */
	protected function resolveErrorUrl($url) {
		if (is_null($url)) {
			$url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
		} elseif ($url) {
			$url = $this->resolveUrl($url);
		}

		return $url;
	}

	/**
	 * 解决url问题
	 *
	 * @param string $url
	 * @return string
	 */
	private function resolveUrl($url) {
		return (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : (string)url($url ?: '');
	}

	/**
	 * @inheritDoc
	 */
	public function outputSuccess($msg, $url = null, $data = null, array $extend = [], callable $callback = null) {
		$this->output(
			$this->success($msg, $url, $data, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputError($msg, $code = 0, $url = null, array $extend = [], callable $callback = null) {
		$this->output(
			$this->error($msg, $code, $url, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputAlert($msg, $code = 0, $url = null, array $extend = [], callable $callback = null) {
		$this->output(
			$this->alert($msg, $code, $url, $extend),
			$callback
		);
	}

	/**
	 * 直接输出
	 *
	 * @param mixed         $response
	 * @param callable|null $callback
	 */
	protected function output($response, callable $callback = null) {
		if (is_callable($callback)) {
			call_user_func($callback, $response);
		}

		throw new HttpResponseException($response);
	}

	/**
	 * @inheritDoc
	 */
	public function alert($msg, $code = 0, $url = null, array $extend = []) {
		return $this->error($msg, $code, $url, array_merge([
			'tips_type' => 'alert',
		], $extend));
	}

}
