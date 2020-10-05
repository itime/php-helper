<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Menu;

use Xin\Menu\MenuManager;
use Xin\Support\Str;

trait InteractsMenuService{
	
	/**
	 * 菜单初始化
	 *
	 * @param \think\Request $request
	 */
	protected function menuInit($request){
		bind('menu', function() use ($request){
			$rule = $this->currentRouteRule($request);
			
			/** @var MenuManager $manager */
			$manager = app(MenuManager::class, [
				'app' => app(),
			]);
			
			[$menus, $breads] = $manager->generate([
				'rule'  => $rule,
				'query' => $request->get() + $request->route(),
				'menus' => $this->menus(),
			]);
			
			return tap(new \stdClass(), function($std) use ($menus, $breads){
				$std->menus = $menus;
				$std->breads = $breads;
			});
		});
	}
	
	/**
	 * 获取生成规则
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function currentRouteRule($request){
		$controller = Str::snake($request->controller());
		$action = $request->action(false);
		$rule = "{$controller}/{$action}";
		
		if(method_exists($request, 'plugin') && $plugin = $request->plugin()){
			$rule = "{$plugin}>{$rule}";
		}
		
		return $rule;
	}
	
	/**
	 * @return array
	 */
	protected function menus(){
		return config('menus');
	}
}
