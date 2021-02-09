<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface AppEnv{
	
	/**
	 * 是否是开发环境
	 *
	 * @return bool
	 */
	public function isDevelop();
	
	/**
	 * 是否是本地环境
	 *
	 * @return bool
	 */
	public function isLocal();
	
	/**
	 * 是否是生产环境
	 *
	 * @return bool
	 */
	public function isProduction();
	
	/**
	 * 是否是所属环境
	 *
	 * @param string ...$env
	 * @return bool
	 */
	public function isEnv(...$env);
}
