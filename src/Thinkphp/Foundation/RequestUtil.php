<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation;

use think\facade\App;
use Xin\Thinkphp\Plugin\Url;

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

		if(Url::$pluginPrefix && strpos($path, Url::$pluginPrefix."/") === 0){
			$info = explode('/', $path, 3);
			if(isset($info[1])){
				$plugin = $info[1];
				$path = isset($info[2]) ? $info[2] : '';

				$path = $plugin.">".$path;
			}
		}

		return $path;
	}
}
