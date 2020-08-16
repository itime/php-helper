<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Hint;

interface Factory{
	
	/**
	 * 使用Ajax
	 *
	 * @return static
	 */
	public function useApi();
	
	/**
	 * 使用Web
	 *
	 * @return static
	 */
	public function useWeb();
	
	/**
	 * 设置使用完成器
	 *
	 * @param \Closure $resolver
	 * @return static
	 */
	public function resolveUsing(\Closure $resolver);
}
