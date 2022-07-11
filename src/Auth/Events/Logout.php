<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Events;

class Logout
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
	 * Create a new event instance.
	 *
	 * @param \Xin\Contracts\Auth\Guard $guard
	 * @param mixed $user
	 * @return void
	 */
	public function __construct($guard, $user, $guardName = null)
	{
		$this->user = $user;
		$this->guard = $guard;
		$this->guardName = $guardName;
	}

}
