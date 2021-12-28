<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation;

use think\facade\App;

class RequestUtil{

	/**
	 * 获取路径规则
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	public static function getPathRule($request = null){
		$request = $request ?: App::make('request');
		$path = $request->path();

		if(method_exists($request, 'plugin') && $plugin = $request->plugin()){
			$path = substr($path, strpos($path, '/', 7) + 1);
			$path = $plugin.">".$path;
		}

		return $path;
	}
}
