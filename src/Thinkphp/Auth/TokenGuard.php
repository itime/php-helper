<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\Container;
use Xin\Auth\AbstractStatefulGuard;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

/**
 * Class TokenUser
 */
class TokenGuard extends AbstractStatefulGuard
{

	use EventHelpers, TokenGuardHelpers;

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * @var \think\Cache
	 */
	protected $cache;

	/**
	 * TokenGuard constructor.
	 *
	 * @param string $name
	 * @param array $config
	 * @param UserProviderContract $provider
	 */
	public function __construct($name, array $config, UserProviderContract $provider)
	{
		parent::__construct($name, $config, $provider);

		$this->app = Container::getInstance();

		$this->request = $this->app['request'];
		$this->cache = $this->app['cache'];

		$this->authTokenResolver = $this->getDefaultAuthTokenResolver();
	}

	/**
	 * @inheritDoc
	 */
	public function logout()
	{
		parent::logout();
		$this->cache->delete($this->getAuthToken());
	}

	/**
	 * @inheritDoc
	 */
	protected function updateSession($user)
	{
		$this->cache->set($this->getAuthToken(), $user);
	}

	/**
	 * @inheritDoc
	 */
	protected function resolveUser()
	{
		return $this->cache->get($this->getAuthToken());
	}

}
