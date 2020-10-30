<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\Request;
use Xin\Auth\Events\Registered;
use Xin\Thinkphp\Facade\Auth;

/**
 * Trait RegistersUsers
 */
trait RegistersUsers{
	
	use RedirectsUsers;
	
	/**
	 * Show the application registration form.
	 *
	 * @return \think\response\View
	 */
	protected function showRegistrationForm(){
		return view('auth/register');
	}
	
	/**
	 * Handle a registration request for the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\Response|\think\response\Redirect
	 */
	public function register(Request $request){
		$data = $this->validateRegister($request);
		
		$user = $this->create($data);
		
		event(new Registered($user));
		
		$this->guard()->login($user);
		
		return $this->registered($request, $user)
			?: redirect($this->redirectPath());
	}
	
	/**
	 * Get the guard to be used during registration.
	 *
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	protected function guard(){
		return Auth::guard();
	}
	
	/**
	 * The user has been registered.
	 *
	 * @param Request $request
	 * @param mixed   $user
	 * @return mixed|void
	 */
	protected function registered(Request $request, $user){
	}
	
	/**
	 * 创建用户
	 *
	 * @param array $data
	 * @return mixed
	 */
	abstract protected function create($data);
}
