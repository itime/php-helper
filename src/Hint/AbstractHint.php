<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Hint;

use Xin\Contracts\Hint\Hint as HintContract;

abstract class AbstractHint implements HintContract{

	/**
	 * @inheritDoc
	 */
	public function result($data = [], array $extend = []){
		return $this->success('OK', null, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function alert($msg, $code = 0, $url = null, array $extend = []){
		return $this->error($msg, $code, $url, array_merge([
			'tips_type' => 'alert',
		], $extend));
	}

	/**
	 * @inheritDoc
	 */
	public function outputSuccess($msg, $url = null, $data = null, array $extend = [], callable $callback = null){
		$this->output(
			$this->success($msg, $url, $data, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputError($msg, $code = 0, $url = null, array $extend = [], callable $callback = null){
		$this->output(
			$this->error($msg, $code, $url, $extend),
			$callback
		);
	}

	/**
	 * @inheritDoc
	 */
	public function outputAlert($msg, $code = 0, $url = null, array $extend = [], callable $callback = null){
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
	abstract protected function output($response, callable $callback = null);
}
