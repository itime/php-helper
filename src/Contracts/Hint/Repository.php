<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Hint;

interface Repository{
	
	/**
	 * 使用Ajax
	 *
	 * @return static
	 */
	public function useAjax();
	
	/**
	 * 使用Web
	 *
	 * @return static
	 */
	public function useWeb();
	
	/**
	 * 设置使用完成器
	 *
	 * @param \Closure $userResolver
	 * @return static
	 */
	public function resolveUsing(\Closure $userResolver);
}
