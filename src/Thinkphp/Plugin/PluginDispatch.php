<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\route\Dispatch;

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
		
		/** @var \Xin\Thinkphp\Plugin\PluginManager $pluginManager */
		$pluginManager = $this->app->get('PlugManager');
		
		return $pluginManager->invoke(
			$this->request,
			$plugin,
			$controller,
			$action
		);
	}
	
}
