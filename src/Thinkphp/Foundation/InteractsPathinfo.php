<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use think\Request;
use Xin\Support\Str;

trait InteractsPathinfo{
	
	/**
	 * 获取当前路由地址（不包含后缀）
	 *
	 * @param bool $complete
	 * @return string
	 */
	public function path($complete = true){
		$route = app()->route;
		$request = $this instanceof Request ? $this : \request();
		
		$suffix = $route->config('url_html_suffix');
		$pathinfo = $request->pathinfo();
		if($complete){
			$pathinfo = $request->root().'/'.$pathinfo;
		}
		$pathinfo = trim($pathinfo, '/');
		
		if(false === $suffix){
			// 禁止伪静态访问
			$path = $pathinfo;
		}elseif($suffix){
			// 去除正常的URL后缀
			$path = preg_replace('/\.('.ltrim($suffix, '.').')$/i', '', $pathinfo);
		}else{
			// 允许任何后缀访问
			$path = preg_replace('/\.'.$this->request->ext().'$/i', '', $pathinfo);
		}
		
		return $path;
	}
	
	/**
	 * 验证当前路由地址是否是给定的规则
	 *
	 * @param string|array $patterns
	 * @return bool
	 */
	public function pathIs($patterns){
		return Str::is($patterns, $this->path());
	}
	
}
