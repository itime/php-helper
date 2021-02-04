<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\exception\HttpException;
use think\facade\Session;
use think\Request;
use think\Validate;
use Xin\Auth\LoginException;
use Xin\Thinkphp\Facade\Auth;
use Xin\Thinkphp\Facade\Hint;

trait AuthenticatesUsers{
	
	use RedirectsUsers, AuthenticatesFields;
	
	/**
	 * Handle a login request to the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\Response|\think\response\Redirect
	 */
	public function login(Request $request){
		if($request->isGet()){
			return $this->showLoginForm($request);
		}
		
		$this->validateLogin($request);
		
		$notExistCallback = method_exists($this, 'notExistUser')
			? \Closure::fromCallable([$this, 'notExistUser']) : null;
		
		try{
			$user = $this->guard()->loginUsingCredential(
				$this->credentials($request),
				$notExistCallback,
				\Closure::fromCallable([$this, 'loginPreCheck'])
			);
			return $this->sendLoginResponse($request, $user);
		}catch(LoginException $e){
			return $this->sendFailedLoginResponse($request, $e);
		}
	}
	
	//	/**
	//	 * The user found does not exist.
	//	 *
	//	 * @param mixed $credentials
	//	 */
	//	protected function notExistUser($credentials){
	//	}
	
	/**
	 * Pre check before login.
	 *
	 * @param mixed $user
	 */
	protected function loginPreCheck($user){
	}
	
	/**
	 * Show the application's login form.
	 *
	 * @param \think\Request $request
	 * @return \think\response\View
	 */
	protected function showLoginForm($request){
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
		
		$validate->rule([
			$this->username() => 'require|alphaDash',
			$this->password() => 'require|alphaDash',
		], [
			$this->username() => '用户名',
			$this->password() => '密码',
		]);
		
		$validate->check($request->post());
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
			$this->password(),
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
		
		if($result = $this->authenticated($request, $user)){
			return $result;
		}
		
		return $request->isJson() || $request->isAjax()
			? Hint::success("登录成功！")
			: redirect($this->redirectPath());
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
	 * @return mixed|void
	 */
	protected function authenticated(Request $request, $user){
	}
	
	/**
	 * Log the user out of the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\response\Redirect
	 */
	public function logout(Request $request){
		if(!$request->isPost()){
			throw new HttpException(500, ' not support '.$request->method());
		}
		
		$this->guard()->logout();
		Session::destroy();
		
		if($result = $this->loggedOut($request)){
			return $result;
		}
		
		return $request->isJson() || $request->isAjax()
			? Hint::success("已退出登录！", (string)url("login/login"))
			: redirect(url("login/login"));
	}
	
	/**
	 * The user has logged out of the application.
	 *
	 * @param Request $request
	 * @return mixed|void
	 */
	protected function loggedOut(Request $request){
	}
	
	/**
	 * 获取守卫者
	 *
	 * @return \Xin\Contracts\Auth\StatefulGuard
	 */
	protected function guard(){
		return Auth::guard();
	}
}
