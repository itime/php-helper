<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

interface AuthVerifyType{
	
	/**
	 * 不验证
	 */
	const NOT = 0;
	
	/**
	 * 验证基础信息
	 */
	const BASE = 1;
}
