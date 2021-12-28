<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Menu;

use think\App;
use Xin\Contracts\Menu\Factory;
use Xin\Menu\MenuManager;
use Xin\Thinkphp\Facade\Auth;
use Xin\Thinkphp\Foundation\RequestUtil;

class Middleware{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * Middleware constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app){
		$this->app = $app;
	}

	/**
	 * 应用初始化
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next){
		$this->request = $request;

		$this->registerServices($request);

		$this->registerDrivers();

		return $next($request);
	}

	/**
	 * 注册服务
	 *
	 * @param \think\Request $request
	 */
	protected function registerServices($request){
		$this->app->bind([
			'menu'             => Factory::class,
			Factory::class     => MenuManager::class,
			MenuManager::class => function(){
				return new MenuManager($this->app, $this->app->config->get('menu'));
			},
			'menu.driver'      => function(){
				return $this->app['menu']->menu();
			},
			'menu.show'        => function() use ($request){
				/** @var \Xin\Menu\MenuManager $manager */
				$manager = $this->app['menu'];

				$this->shouldUse($manager);

				[$menus, $breads] = $manager->generate(
					$this->getFilterResolver(),
					$this->getGenerateOptions()
				);

				$std = new \stdClass();
				$std->menus = $menus;
				$std->breads = $breads;

				return $std;
			},
		]);
	}

	/**
	 * 注册菜单驱动
	 */
	protected function registerDrivers(){
		/** @var MenuManager $manager */
		$manager = $this->app['menu'];

		$manager->extend('model', function($config){
			return new Database($config);
		});
	}

	/**
	 * 获取菜单过滤器
	 *
	 * @return callable|null
	 */
	protected function getFilterResolver(){
		return null;
	}

	/**
	 * 获取生成器配置参数
	 *
	 * @return array
	 */
	protected function getGenerateOptions(){
		return [
			'rule'             => $this->getPathRule(),
			'query'            => $this->getQuery(),
			'is_administrator' => $this->isAdministrator(),
			'is_develop'       => $this->isDevMode(),
		];
	}

	/**
	 * 应该使用哪个菜单
	 *
	 * @param \Xin\Menu\MenuManager $manager
	 */
	protected function shouldUse(MenuManager $manager){
		$manager->shouldUse($this->app->http->getName());
	}

	/**
	 * 获取路径规则
	 *
	 * @return string
	 */
	protected function getPathRule(){
		return RequestUtil::getPathRule($this->request);
	}

	/**
	 * 获取当前请求参数
	 *
	 * @return array
	 */
	protected function getQuery(){
		return $this->request->get() + $this->request->route();
	}

	/**
	 * 是否是超管
	 *
	 * @return bool
	 */
	protected function isAdministrator(){
		return Auth::isAdministrator();
	}

	/**
	 * 是否是开发模式
	 *
	 * @return bool
	 */
	protected function isDevMode(){
		return $this->app->config->get('web.develop_mode') == 1;
	}
}
