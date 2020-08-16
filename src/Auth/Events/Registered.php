<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth\Events;

class Registered{
	
	/**
	 * The authenticated user.
	 *
	 * @var \Xin\Contracts\Auth\Guard
	 */
	public $user;
	
	/**
	 * Create a new event instance.
	 *
	 * @param \Xin\Contracts\Auth\Guard $user
	 * @return void
	 */
	public function __construct($user){
		$this->user = $user;
	}
}
