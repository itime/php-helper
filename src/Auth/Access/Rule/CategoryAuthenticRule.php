<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Rule;

class CategoryAuthenticRule extends AuthenticRule{

	/**
	 * 规则方案
	 */
	const SCHEME_NAME = 'category';

	/**
	 * CategoryAuthenticRule constructor.
	 *
	 * @param string $id
	 */
	public function __construct($id){
		parent::__construct(self::SCHEME_NAME, $id);
	}
}
