<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Events;

class LoginFailed
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
	 * The user the attempter was trying to authenticate as.
	 *
	 * @var mixed
	 */
	public $user;

	/**
	 * The credentials provided by the attempter.
	 *
	 * @var array
	 */
	public $credentials;

	/**
	 * Create a new event instance.
	 *
	 * @param \Xin\Contracts\Auth\Guard $guard
	 * @param mixed $user
	 * @param array $credentials
	 * @return void
	 */
	public function __construct($guard, $user, $credentials, $guardName = null)
	{
		$this->user = $user;
		$this->guard = $guard;
		$this->credentials = $credentials;
		$this->guardName = $guardName;
	}

}
