<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Auth\Events\Logged as LoginEvent;
use Xin\Auth\Events\LoginFailed as LoginFailedEvent;
use Xin\Auth\Events\Logout as LogoutEvent;

/**
 * @property-read \think\App app
 */
trait EventHelpers{
	
	/**
	 * @inheritDoc
	 */
	protected function fireLoginEvent($user, $remember = false){
		$this->app->event->trigger(new LoginEvent($this, $user, $remember));
	}
	
	/**
	 * @inheritDoc
	 */
	protected function fireFailedEvent($user, array $credentials){
		$this->app->event->trigger(new LoginFailedEvent($this, $user, $credentials));
	}
	
	/**
	 * @inheritDoc
	 */
	protected function fireLogoutEvent($user){
		$this->app->event->trigger(new LogoutEvent($this, $user));
	}
}
