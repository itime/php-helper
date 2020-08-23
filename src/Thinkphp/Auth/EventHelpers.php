<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

trait EventHelpers{
	
	/**
	 * @inheritDoc
	 */
	protected function fireLoginEvent($user, $remember = false){
		// TODO: Implement fireLoginEvent() method.
	}
	
	/**
	 * @inheritDoc
	 */
	protected function fireFailedEvent($user, array $credentials){
		// TODO: Implement fireFailedEvent() method.
	}
	
	/**
	 * @inheritDoc
	 */
	protected function fireLogoutEvent($user){
		// TODO: Implement fireLogoutEvent() method.
	}
}
