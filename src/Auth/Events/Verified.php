<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Events;

class Verified
{

	/**
	 * The verified user.
	 *
	 * @var \Xin\Contracts\Auth\MustVerifyEmail
	 */
	public $user;

	/**
	 * Create a new event instance.
	 *
	 * @param \Xin\Contracts\Auth\MustVerifyEmail $user
	 * @return void
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

}
