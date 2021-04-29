<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Menu;

use Xin\Menu\MenuManager;
use Xin\Thinkphp\Facade\Auth;

trait InteractsMenuService{

	/**
	 * 菜单初始化
	 *
	 * @param \think\Request|\Xin\Thinkphp\Http\Requestable $request
	 */
	protected function menuInit($request){
		bind([
			'menu'        => function(){
				return new MenuManager($this->app, config('menu'));
			},
			'menu.driver' => function(){
				return $this->app['menu']->menu();
			},
			'menu.show'   => function() use ($request){
				/** @var \Xin\Menu\MenuManager $manager */
				$manager = $this->app->menu;

				if(property_exists($this, 'shouldUserMenu')){
					$manager->shouldUse($this->shouldUserMenu);
				}

				[$menus, $breads] = $manager->generate($request->user(), [
					'rule'             => $this->getCurrentPathRule($request),
					'query'            => $request->get() + $request->route(),
					'is_administrator' => $this->isAdministrator(),
					'is_develop'       => $this->isDevMode(),
				]);

				$menus = $this->menusHandle($menus);

				return tap(new \stdClass(), function($std) use ($menus, $breads){
					$std->menus = $menus;
					$std->breads = $breads;
				});
			},
		]);
	}

	/**
	 * 获取生成规则
	 *
	 * @param \Xin\Thinkphp\Http\Requestable $request
	 * @return string
	 */
	protected function getCurrentPathRule($request){
		//		$controller = $request->controller();
		//		if($pos = strrpos($controller, '.')){
		//			$controller = substr($controller, 0, $pos).".".Str::snake(substr($controller, $pos + 1));
		//		}else{
		//			$controller = Str::snake($controller);
		//		}
		//
		//		$action = $request->action(false);
		//		$rule = "{$controller}/{$action}";
		//
		//		if(method_exists($request, 'plugin') && $plugin = $request->plugin()){
		//			$rule = "{$plugin}>{$rule}";
		//		}
		//
		//		return $rule;

		$path = $request->path();
		if(strpos($path, "plugin") === 0){
			$pulgin = substr($path, 7, strpos($path, '/', 7) - 7);
			$path = substr($path, strpos($path, '/', 7) + 1);
			$path = $pulgin.">".$path;
		}

		return $path;
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
		return config('web.develop_mode') == 1;
	}

	/**
	 * @param array $menus
	 * @return array
	 */
	protected function menusHandle($menus){
		return $menus;
	}
}
