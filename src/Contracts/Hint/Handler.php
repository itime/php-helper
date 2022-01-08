<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Hint;

interface Handler {

	/**
	 * 渲染
	 * @param mixed $data
	 * @return mixed
	 */
	public function render($data);

	/**
	 * 渲染并输出
	 * @param mixed         $response
	 * @param callable|null $callback
	 */
	public function output($response, callable $callback = null);

	/**
	 * 是否是 Ajax 渲染
	 * @return bool
	 */
	public function isAjax();

	/**
	 * 生成 Url
	 * @param string $url
	 * @return string
	 */
	public function url($url);

}
