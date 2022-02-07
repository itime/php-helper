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
	 * @var \Xin\Contracts\Auth\Guard
	 */
	public $guard;

	/**
	 * @var string
	 */
	public $guardName;

	/**
	 * The authenticated user.
	 *
	 * @var mixed
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
	 * @param \Xin\Contracts\Auth\Guard $guard
	 * @param mixed $user
	 * @param bool $remember
	 * @return void
	 */
	public function __construct($guard, $user, $remember, $guardName = null)
	{
		$this->user = $user;
		$this->guard = $guard;
		$this->remember = $remember;
		$this->guardName = $guardName;
	}

}
