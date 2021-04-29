<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth\Events;

class PasswordReset{

	/**
	 * The user.
	 *
	 * @var mixed
	 */
	public $user;

	/**
	 * Create a new event instance.
	 *
	 * @param \Illuminate\Contracts\Auth\Authenticatable $user
	 * @return void
	 */
	public function __construct($user){
		$this->user = $user;
	}
}
