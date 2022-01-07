<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth;

use Xin\Contracts\Auth\StatefulGuard as StatefulGuardContract;

abstract class AbstractStatefulGuard extends AbstractGuard implements StatefulGuardContract {

	/**
	 * @inheritDoc
	 */
	public function temporaryUser($user, $abort = true) {
		if (!$this->check()) {
			return;
		}

		$this->updateSession($user);

		parent::temporaryUser($user);
	}

	/**
	 * @inheritDoc
	 */
	public function login($user) {
		$this->updateSession($user);

		$this->user = $user;

		$this->fireLoginEvent($user);

		return $user;
	}

	/**
	 * @inheritDoc
	 */
	public function loginUsingId($id) {
		$user = $this->provider->getById($id);
		if (empty($user)) {
			throw new LoginException('用户不存在!', 40401);
		}

		return $this->login($user);
	}

	/**
	 * @inheritDoc
	 */
	public function loginUsingCredential(
		array    $credentials,
		\Closure $notExistCallback = null,
		\Closure $preCheckCallback = null
	) {
		$user = $this->credentials($credentials, $notExistCallback);

		// password field exist.
		if ($this->hasPasswordInCredential($credentials)) {
			$password = $this->resolvePasswordInCredential($credentials);
			if (!$this->provider->validatePassword($user, $password)) {
				// If the authentication attempt fails we will fire an event so that the user
				// may be notified of any suspicious attempts to access their account from
				// an unrecognized user. A developer may listen to this event as needed.
				$this->fireFailedEvent($user, $credentials);

				throw new LoginException('账号密码不正确', 40001);
			}
		}

		if (is_callable($preCheckCallback)) {
			call_user_func($preCheckCallback, $user);
		}

		return $this->login($user);
	}

	/**
	 * 密码字段是否存在凭证信息里面
	 *
	 * @param array $credentials
	 * @return bool
	 */
	protected function hasPasswordInCredential($credentials) {
		$passwordName = $this->provider->getPasswordName();

		return isset($credentials[$passwordName]);
	}

	/**
	 * 从凭证信息里面获取密码数据
	 *
	 * @param array $credentials
	 * @return mixed
	 */
	protected function resolvePasswordInCredential(array $credentials) {
		$passwordName = $this->provider->getPasswordName();

		return $credentials[$passwordName];
	}

	/**
	 * @param array|\Closure $credentials
	 * @param \Closure|null  $notExistCallback
	 * @return mixed
	 */
	protected function credentials(array $credentials, \Closure $notExistCallback = null) {
		if (isset($credentials['password'])) {
			unset($credentials['password']);
		}

		$user = $this->provider->getByCredentials($credentials);
		if (empty($user)) {
			if (is_callable($notExistCallback)) {
				$user = call_user_func($notExistCallback, $credentials);
			} else {
				throw new LoginException("用户不存在！", 40402);
			}
		}

		return $user;
	}

	/**
	 * @inheritDoc
	 */
	public function logout() {
		$this->fireLogoutEvent($this->user);

		$this->user = null;
	}

	/**
	 * 获取一个Session的唯一名称
	 *
	 * @return string
	 */
	public function getName() {
		return 'login_' . $this->name . '_' . sha1(static::class);
	}

	/**
	 * 缓存用户模型
	 *
	 * @param mixed $user
	 * @return string
	 */
	protected function makeAuthSign($user) {
		return sha1(md5($user['id']) . time());
	}

	/**
	 * 更新用户 Session
	 *
	 * @param mixed $user
	 * @return mixed
	 */
	abstract protected function updateSession($user);

	/**
	 * 触发登录事件
	 *
	 * @param mixed $user
	 * @param bool  $remember
	 */
	abstract protected function fireLoginEvent($user, $remember = false);

	/**
	 * 触发登录失败的事件
	 *
	 * @param mixed $user
	 * @param array $credentials
	 */
	abstract protected function fireFailedEvent($user, array $credentials);

	/**
	 * 触发退出登录事件
	 *
	 * @param mixed $user
	 */
	abstract protected function fireLogoutEvent($user);

}
