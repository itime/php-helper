<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\facade\Session;
use think\Request;
use think\Validate;
use Xin\Auth\LoginException;
use Xin\Thinkphp\Auth\Facade\Auth;
use Xin\Thinkphp\Hint\Facade\Hint;

trait AuthenticatesUsers{
	
	use RedirectsUsers;
	
	/**
	 * Handle a login request to the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\Response|\think\response\Redirect
	 */
	public function login(Request $request){
		if($request->isGet()){
			return $this->showLoginForm();
		}
		
		$this->validateLogin($request);
		
		try{
			$user = $this->guard()->loginUsingCredential(
				$this->credentials($request)
			);
			
			return $this->sendLoginResponse($request, $user);
		}catch(LoginException $e){
			return $this->sendFailedLoginResponse($request, $e);
		}
	}
	
	/**
	 * Show the application's login form.
	 *
	 * @return \think\response\View
	 */
	protected function showLoginForm(){
		return view('auth/login');
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
		$validate->check($request->param(), [
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
	 * @param mixed   $user
	 * @return mixed|\think\response\Redirect
	 */
	protected function sendLoginResponse(Request $request, $user){
		Session::regenerate();
		
		return $this->authenticated($request, $user)
			?: redirect($this->redirectPath());
	}
	
	/**
	 * Get the failed login response instance.
	 *
	 * @param Request                  $request
	 * @param \Xin\Auth\LoginException $e
	 * @return \think\Response
	 */
	protected function sendFailedLoginResponse(Request $request, LoginException $e){
		return Hint::error($e->getMessage());
	}
	
	/**
	 * The user has been authenticated.
	 *
	 * @param Request $request
	 * @param mixed   $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user){
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
		
		Session::destroy();
		
		return $this->loggedOut($request) ?: redirect('/');
	}
	
	/**
	 * The user has logged out of the application.
	 *
	 * @param Request $request
	 * @return mixed
	 */
	protected function loggedOut(Request $request){
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
