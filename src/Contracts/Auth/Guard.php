<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Auth;

/**
 * Interface Guard
 */
interface Guard{

	/**
	 * 尝试从本地缓存获取守卫者
	 *
	 * @param string|null $name
	 * @return \Xin\Auth\UserInterface
	 */
	public function guard($name = null);

	/**
	 * 选择一个默认的守卫者
	 *
	 * @param string $name
	 * @return \Xin\Auth\UserInterface
	 */
	public function shouldUse($name);
}
