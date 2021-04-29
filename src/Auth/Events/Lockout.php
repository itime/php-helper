<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Auth\Events;

class Lockout{

	/**
	 * The throttled request.
	 */
	public $request;

	/**
	 * Create a new event instance.
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function __construct($request){
		$this->request = $request;
	}
}
