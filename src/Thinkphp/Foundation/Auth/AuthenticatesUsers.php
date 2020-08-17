<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\exception\ValidateException;
use think\facade\Session;
use think\Request;
use think\Validate;
use Xin\Thinkphp\Auth\Facade\Auth;

trait AuthenticatesUsers{
	
	use RedirectsUsers;
	
	/**
	 * Show the application's login form.
	 *
	 * @return \think\response\View
	 */
	public function showLoginForm(){
		return view('auth.login');
	}
	
	/**
	 * Handle a login request to the application.
	 *
	 * @param Request $request
	 */
	public function login(Request $request){
		$this->validateLogin($request);
		
		if($this->guard()->loginUsingCredential(
			$this->credentials($request)
		)){
			return $this->sendLoginResponse($request);
		}
		
		return $this->sendFailedLoginResponse($request);
	}
	
	/**
	 * Validate the user login request.
	 *
	 * @param Request $request
	 * @return void
	 */
	protected function validateLogin(Request $request){
		$validate = new Validate();
		$validate->failException(true);
		$validate->check([
			$this->username() => 'required|string',
			'password'        => 'required|string',
		]);
	}
	
	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param Request $request
	 * @return array
	 */
	protected function credentials(Request $request){
		return $request->only([
			$this->username(),
			'password',
		]);
	}
	
	/**
	 * Send the response after the user was authenticated.
	 *
	 * @param Request $request
	 * @return mixed|\think\response\Redirect
	 */
	protected function sendLoginResponse(Request $request){
		Session::regenerate();
		
		return $this->authenticated(
			$request,
			$this->guard()->getUserInfo(null, null, false)
		) ?: redirect($this->redirectPath());
	}
	
	/**
	 * The user has been authenticated.
	 *
	 * @param Request $request
	 * @param mixed   $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user){
		//
	}
	
	/**
	 * Get the failed login response instance.
	 *
	 * @param Request $request
	 * @return \think\Response
	 */
	protected function sendFailedLoginResponse(Request $request){
		throw new ValidateException('登录失败！');
	}
	
	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username(){
		return 'username';
	}
	
	/**
	 * Log the user out of the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\response\Redirect
	 */
	public function logout(Request $request){
		$this->guard()->logout();
		
		$request->session()->invalidate();
		
		return $this->loggedOut($request) ?: redirect('/');
	}
	
	/**
	 * The user has logged out of the application.
	 *
	 * @param Request $request
	 * @return mixed
	 */
	protected function loggedOut(Request $request){
		//
	}
	
	/**
	 * 获取守卫者
	 *
	 * @return \Xin\Contracts\Auth\Guard
	 */
	protected function guard(){
		return Auth::guard();
	}
}
