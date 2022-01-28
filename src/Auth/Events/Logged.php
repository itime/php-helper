<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Events;

class Logged
{

	/**
	 * The authentication guard name.
	 *
	 * @var string
	 */
	public $guard;

	/**
	 * The authenticated user.
	 *
	 * @var \Xin\Contracts\Auth\Guard
	 */
	public $user;

	/**
	 * Indicates if the user should be "remembered".
	 *
	 * @var bool
	 */
	public $remember;

	/**
	 * Create a new event instance.
	 *
	 * @param string $guard
	 * @param \Xin\Contracts\Auth\Guard $user
	 * @param bool $remember
	 * @return void
	 */
	public function __construct($guard, $user, $remember)
	{
		$this->user = $user;
		$this->guard = $guard;
		$this->remember = $remember;
	}

}
