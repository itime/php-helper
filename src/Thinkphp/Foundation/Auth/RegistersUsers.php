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
use Xin\Auth\Events\Registered;
use Xin\Thinkphp\Facade\Auth;

/**
 * Trait RegistersUsers
 */
trait RegistersUsers{

	use RedirectsUsers, AuthenticatesFields;

	/**
	 * Show the application registration form.
	 *
	 * @param \think\Request $request
	 * @return \think\response\View
	 */
	protected function showRegistrationForm(Request $request){
		return view('auth/register');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param Request $request
	 * @return mixed|\think\Response|\think\response\Redirect
	 */
	public function register(Request $request){
		if($request->isGet()){
			return $this->showRegistrationForm($request);
		}

		$data = $this->validateRegister($request);

		$data = $this->encryptPassword($data);

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

	/**
	 * 验证数据合法性
	 *
	 * @param \think\Request $request
	 * @return array
	 */
	protected function validateRegister(Request $request){
		$validate = new Validate();
		$validate->failException(true);

		$validate->rule([
			$this->username()        => 'require|alphaDash',
			$this->password()        => 'require|alphaDash|confirm:'.$this->confirmPassword(),
			$this->confirmPassword() => "require|alphaDash",
		], [
			$this->username() => '用户名',
			$this->password() => '密码',
			$this->password() => '确认密码',
		]);

		$data = $request->post();
		$validate->check($data);

		// 验证账号是否被注册过
		$username = $data[$this->username()];
		/** @var \Xin\Contracts\Auth\UserProvider $provider */
		$provider = $this->guard()->getProvider();
		$info = $provider->getByCredentials([
			$this->username() => $username,
		]);
		if(!empty($info)){
			throw new ValidateException("该账号已被注册！");
		}

		return $data;
	}

	/**
	 * 加密密码
	 *
	 * @param array $data
	 * @return array
	 */
	protected function encryptPassword($data){
		$passwordField = $this->password();
		$data[$passwordField] = app('hash')->make($data[$passwordField]);

		return $data;
	}
}
