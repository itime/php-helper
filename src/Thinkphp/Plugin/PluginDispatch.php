<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\facade\Route;
use think\route\Dispatch;
use Xin\Thinkphp\Plugin\Facade\Plugin;

/**
 * Class PluginDispatch
 *
 * @property-read \think\Request|\Xin\Thinkphp\Http\RequestOptimize $request
 */
class PluginDispatch extends Dispatch{
	
	/**
	 * @return mixed
	 */
	public function exec(){
		$plugin = $this->param['plugin'];
		$controller = $this->param['controller'];
		$action = $this->param['action'];
		
		return Plugin::invokeAction(
			$this->request,
			$plugin,
			$controller,
			$action
		);
	}
	
	/**
	 * 路由到自定义调度对象
	 */
	public static function routes(){
		Route::get('plugin/:plugin/:controller/:action', static::class);
	}
}
