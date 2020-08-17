<?php

namespace Xin\Thinkphp\Auth\Events;

use think\Request;

class Lockout{
	
	/**
	 * The throttled request.
	 */
	public $request;
	
	/**
	 * Create a new event instance.
	 *
	 * @param Request $request
	 * @return void
	 */
	public function __construct(Request $request){
		$this->request = $request;
	}
}
