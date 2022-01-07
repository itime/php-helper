<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth\Access;

use think\App;
use Xin\Support\Str;
use Xin\Thinkphp\Foundation\RequestUtil;

class CheckForRoute {

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * @param \think\App $app
	 */
	public function __construct(App $app) {
		$this->app = $app;
		$this->request = $app['request'];
	}

	/**
	 * 处理器
	 *
	 * @param mixed  $user
	 * @param string $checkUrl
	 * @return bool
	 */
	public function handle($user, $checkUrl) {
		if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
			return true;
		}

		if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
			return true;
		}

		$menu = $this->findByUrl($checkUrl);
		if (!$menu) {
			return false;
		}

		return $this->isOwn($user, $menu);
	}

	/**
	 * @param string $url
	 * @return array
	 */
	protected function findByUrl($url) {
		/** @var \Xin\Menu\MenuManager $menuManager */
		$menuManager = $this->app['menu'];
		$menus = $menuManager->all();

		$currentPath = $this->getCurrentPath();
		$currentQuery = $this->getCurrentQuery();

		foreach ($menus as $menu) {
			$url = $menu['url'] ?? $menu['name'];
			if (Str::matchUrl($url, $currentPath, $currentQuery)) {
				return $menu;
			}
		}

		return null;
	}

	/**
	 * 是否拥有菜单权限
	 *
	 * @param mixed $user
	 * @param array $menu
	 * @return void
	 */
	protected function isOwn($user, $menu) {
		if (!method_exists($user, 'getAllMenuIds')) {
			return false;
		}

		return in_array($menu['id'], $user->getAllMenuIds());
	}

	/**
	 * @return string
	 */
	protected function getCurrentPath() {
		return RequestUtil::getPathRule($this->request);
	}

	/**
	 * @return array
	 */
	protected function getCurrentQuery() {
		return $this->request->get() + $this->request->route();
	}

}
