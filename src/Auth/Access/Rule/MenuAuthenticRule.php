<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Access\Rule;

class MenuAuthenticRule extends AuthenticRule{
	
	/**
	 * 规则方案
	 */
	const SCHEME_NAME = 'url';
	
	/**
	 * MenuAuthenticRule constructor.
	 *
	 * @param string $rule
	 */
	public function __construct($rule){
		parent::__construct(self::SCHEME_NAME, $rule);
	}
}
