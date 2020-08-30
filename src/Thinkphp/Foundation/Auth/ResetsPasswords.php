<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\Request;
use think\Validate;
use Xin\Auth\Events\PasswordReset;
use Xin\Thinkphp\Facade\Auth;
use Xin\Thinkphp\Facade\Hash;
use Xin\Thinkphp\Facade\Hint;

trait ResetsPasswords{
	
	use RedirectsUsers;
	
	/**
	 * Reset the given user's password.
	 *
	 * @param \think\Request $request
	 * @return mixed
	 */
	public function reset(Request $request){
		if($request->isGet()){
			return $this->showResetForm($request);
		}
		
		$this->validateReset($request);
		
		$credentials = $this->credentials($request);
		$user = $this->guard()->getUser();
		if($this->resetPassword($user, $credentials)){
			return $this->sendResetResponse($request, $credentials);
		}
		
		// If the password was successfully reset, we will redirect the user back to
		// the application's home authenticated view. If there is an error we can
		// redirect them back to where they came from with their error message.
		return $this->sendResetFailedResponse($request, $credentials);
	}
	
	/**
	 * Display the password reset view for the given token.
	 * If no token is present, display the link request form.
	 *
	 * @param \think\Request $request
	 * @return \think\response\View
	 */
	public function showResetForm(Request $request){
		return view('auth/password_reset');
	}
	
	/**
	 * Validate the user login request.
	 *
	 * @param Request $request
	 * @return void
	 */
	protected function validateReset(Request $request){
		$validate = new Validate();
		$validate->failException(true);
		
		$validate->rule([
			'password'    => 'require|alphaDash',
			'repassword'  => 'require|confirm:password',
			'newpassword' => 'require|alphaDash|different:password',
		], [
			'password'    => '旧密码',
			'repassword'  => '确认密码',
			'newpassword' => '新密码',
		]);
		
		$validate->check($request->param());
	}
	
	/**
	 * Get the password reset credentials from the request.
	 *
	 * @param \think\Request $request
	 * @return array
	 */
	protected function credentials(Request $request){
		return $request->only([
			'password',
			'repassword',
			'newpassword',
		]);
	}
	
	/**
	 * Reset the given user's password.
	 *
	 * @param \think\Model $user
	 * @param array        $credentials
	 * @return bool
	 */
	protected function resetPassword($user, array $credentials){
		$user->password = Hash::make($credentials['newpassword']);
		if($user->save() === false){
			return false;
		}
		
		event(new PasswordReset($user));
		
		$this->guard()->login($user);
		
		return true;
	}
	
	/**
	 * Get the response for a successful password reset.
	 *
	 * @param \think\Request $request
	 * @param array          $credentials
	 * @return \think\Response
	 */
	protected function sendResetResponse(Request $request, array $credentials){
		return $request->isJson() || $request->isAjax()
			? Hint::success("修改成功！", $this->redirectPath())
			: redirect($this->redirectPath());
	}
	
	/**
	 * Get the response for a failed password reset.
	 *
	 * @param \think\Request $request
	 * @param array          $credentials
	 * @return \think\Response
	 */
	protected function sendResetFailedResponse(Request $request, array $credentials){
		return Hint::error('修改失败！');
	}
	
	/**
	 * Get the guard to be used during password reset.
	 *
	 * @return \Xin\Contracts\Auth\Guard|\Xin\Contracts\Auth\StatefulGuard
	 */
	protected function guard(){
		return Auth::guard();
	}
}
