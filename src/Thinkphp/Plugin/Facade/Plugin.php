<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Plugin\Facade;

use think\Facade;

/**
 * Class Plugin
 * @method bool has($plugin) static
 * @method string path($plugin) static
 * @method string classPath($plugin) static
 * @method string controllerPath($plugin, $controller, $layer = 'controller') static
 * @method static invokeAction(\think\Request $request, $plugin, $controller, $action)
 */
class Plugin extends Facade{
	
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass(){
		return 'PlugManager';
	}
}
