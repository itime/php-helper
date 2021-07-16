<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Hint;

interface Factory{

	/**
	 * 使用 Api 提示器
	 *
	 * @return static
	 */
	public function shouldUseApi();

	/**
	 * 使用 Web 提示器
	 *
	 * @return static
	 */
	public function shouldUseWeb();

	/**
	 * 使用指定的提示器
	 *
	 * @param string $name
	 * @return static
	 */
	public function shouldUse($name);

	/**
	 * 获取提示器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Hint\Hint
	 */
	public function hint($name = null);
}
