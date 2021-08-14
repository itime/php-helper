<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus\Balance;

use Xin\Bus\Balance\SceneEnum;

/**
 * Trait HasDatabaseBalance
 */
trait HasDatabaseBalance{

	/**
	 * 余额消费场景
	 *
	 * @return string
	 */
	protected function getSceneTextAttr(){
		$val = $this->getOrigin('scene');
		return SceneEnum::data()[$val]['title'];
	}
}
