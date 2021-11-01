<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Auth;

use think\exception\ValidateException;
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

		// 验证请求数据是否正确
		$credentials = $this->validateReset($request);

		$user = $this->guard()->getUser();

		// 验证原始密码是否正确
		$this->validateOriginalPassword($user, $credentials);

		// 重新设置新密码
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
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function showResetForm(Request $request){
		return view('auth/password_reset');
	}

	/**
	 * Validate the user login request.
	 *
	 * @param Request $request
	 * @return array
	 */
	protected function validateReset(Request $request){
		$validate = new Validate();
		$validate->failException(true);

		$passwordField = $this->password();
		$newPasswordField = $this->newPassword();
		$confirmPasswordField = $this->confirmPassword();

		$validate->rule([
			$passwordField        => 'require|alphaDash',
			$newPasswordField     => "require|password|length:6,16|different:{$passwordField}",
			$confirmPasswordField => "require|confirm:{$newPasswordField}",
		], [
			'password'         => '旧密码',
			'new_password'     => '新密码',
			'confirm_password' => '确认密码',
		]);
		$validate->message([
			"{$newPasswordField}.different"   => '新密码不能与旧密码一样',
			"{$confirmPasswordField}.confirm" => '新密码与确认密码不一致',
		]);

		$credentials = $this->credentials($request);
		$validate->check($credentials);

		return $credentials;
	}

	/**
	 * 验证原始密码是否正确
	 *
	 * @param mixed $user
	 * @param array $credentials
	 */
	protected function validateOriginalPassword($user, $credentials){
		$originalPassword = $credentials[$this->password()];

		$authPasswordField = $this->authPassword();

		if(!Hash::check($originalPassword, $user[$authPasswordField])){
			throw new ValidateException("原始密码不一致");
		}
	}

	/**
	 * Get the password reset credentials from the request.
	 *
	 * @param \think\Request $request
	 * @return array
	 */
	protected function credentials(Request $request){
		return $request->only([
			$this->password(),
			$this->newPassword(),
			$this->confirmPassword(),
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
		$authPasswordField = $this->authPassword();

		$user[$authPasswordField] = Hash::make(
			$credentials[$this->newPassword()]
		);

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
	 * @noinspection PhpUnusedParameterInspection
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
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function sendResetFailedResponse(Request $request, array $credentials){
		return Hint::error('修改失败！');
	}

	/**
	 * 守卫者密码字段
	 *
	 * @return string
	 */
	protected function authPassword(){
		return 'password';
	}

	/**
	 * 旧密码
	 *
	 * @return string
	 */
	protected function password(){
		return 'password';
	}

	/**
	 * 新密码
	 *
	 * @return string
	 */
	protected function newPassword(){
		return 'new_password';
	}

	/**
	 * 确认密码
	 *
	 * @return string
	 */
	protected function confirmPassword(){
		return 'confirm_password';
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
