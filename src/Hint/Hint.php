<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Hint;

use Xin\Capsule\Service;
use Xin\Contracts\Hint\Handler;
use Xin\Contracts\Hint\Hint as HintContract;

class Hint extends Service implements HintContract
{

	/**
	 * @var Handler
	 */
	protected $handler;

	/**
	 * @param array $config
	 * @param Handler $handler
	 */
	public function __construct(array $config, Handler $handler)
	{
		parent::__construct($config);

		$this->handler = $handler;

		if (method_exists($this->handler, 'setHintService')) {
			$this->handler->setHintService($this);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function result($data = [], array $extend = [])
	{
		return $this->success('OK', null, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function success($msg, $url = null, $data = null, array $extend = [])
	{
		if (!$this->handler->isAjax() || $url) {
			$url = $this->successUrl($url);
			$extend['url'] = $url;
		}

		return $this->render(1, $msg, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function error($msg, $code = 0, $url = null, array $extend = [])
	{
		if ($msg instanceof \Exception) {
			$code = $msg->getCode();
			$msg = $msg->getMessage();
			$extend = is_array($code) ? $code : [];
		}
		$extend['url'] = (string)$url;

		return $this->render($code, $msg, null, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function alert($msg, $code = 0, $url = null, array $extend = [])
	{
		return $this->error($msg, $code, $url, array_merge([
			'tips_type' => 'alert',
		], $extend));
	}

	/**
	 * make Response
	 *
	 * @param string $code
	 * @param string $msg
	 * @param mixed $data
	 * @param array $extend
	 * @return \think\response
	 */
	protected function render($code, $msg, $data, array $extend = [])
	{
		return $this->handler->render(array_merge([
			'code' => $code,
			'msg' => $msg,
			'data' => $data,
		], $extend));
	}

	/**
	 * @inheritDoc
	 */
	public function outputSuccess($msg, $url = null, $data = null, array $extend = [], callable $callback = null)
	{
		$this->output(
			$this->success($msg, $url, $data, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputError($msg, $code = 0, $url = null, array $extend = [], callable $callback = null)
	{
		$this->output(
			$this->error($msg, $code, $url, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputAlert($msg, $code = 0, $url = null, array $extend = [], callable $callback = null)
	{
		$this->output(
			$this->alert($msg, $code, $url, $extend),
			$callback
		);
	}

	/**
	 * 直接输出
	 *
	 * @param mixed $response
	 * @param callable|null $callback
	 */
	protected function output($response, callable $callback = null)
	{
		$this->handler->output($response, $callback);
	}

	/**
	 * @param string $url
	 * @return string
	 */
	protected function successUrl($url)
	{
		if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
			$url = $_SERVER["HTTP_REFERER"];
		} elseif ($url) {
			$url = $this->handler->url($url);
		}

		return $url;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	protected function errorUrl($url)
	{
		if (is_null($url)) {
			$url = $this->handler->isAjax() ? '' : 'javascript:history.back(-1);';
		} elseif ($url) {
			$url = $this->handler->url($url);
		}

		return $url;
	}

}
