<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Auth;

/**
 * Interface Factory
 */
interface Factory{
	
	/**
	 * 尝试从本地缓存获取守卫者
	 *
	 * @param string|null $name
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	public function guard($name = null);
	
	/**
	 * 选择一个默认的守卫者
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	public function shouldUse($name);
}
