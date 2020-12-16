<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth\Access;

interface AuthenticRule{
	
	/**
	 * 获取规则方案
	 *
	 * @return string
	 */
	public function getScheme();
	
	/**
	 * 获取规则实体
	 *
	 * @return string
	 */
	public function getEntity();
	
	/**
	 * 获取扩展参数
	 *
	 * @return array
	 */
	public function getOptions();
}
