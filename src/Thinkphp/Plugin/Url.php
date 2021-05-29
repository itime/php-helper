<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Plugin;

use think\app\Url as UrlBuild;
use Xin\Support\Str;

/**
 * 路由地址生成
 */
class Url extends UrlBuild{

	/**
	 * 直接解析URL地址
	 *
	 * @access protected
	 * @param string      $url URL
	 * @param string|bool $domain Domain
	 * @return string
	 */
	protected function parseUrl(string $url, &$domain):string{
		/** @var \think\Request|\Xin\Thinkphp\Http\Requestable $request */
		$request = $this->app->request;

		// 支持路由解析
		if(strpos($url, '>')){
			[$plugin, $url] = explode('>', $url, 2);
		}else{
			$plugin = '';
		}

		if(0 === strpos($url, '/')){
			// 直接作为路由地址解析
			$url = substr($url, 1);
		}elseif(false !== strpos($url, '\\')){
			// 解析到类
			$url = ltrim(str_replace('\\', '/', $url), '/');
		}elseif(0 === strpos($url, '@')){
			// 解析到控制器
			$url = substr($url, 1);
		}elseif('' === $url){
			$url = $this->getAppName().'/'
				.($plugin ? "plugin/".$plugin."/" : "")
				.$this->parseController($request->controller()).'/'
				.$request->action();
		}else{
			// 解析到 应用/控制器/操作
			$app = $this->getAppName();
			$controller = Str::snake($request->controller());

			$path = explode('/', $url);
			$action = array_pop($path);
			$controller = empty($path) ? $controller : array_pop($path);
			$app = empty($path) ? $app : array_pop($path);

			$bind = $this->app->config->get('app.domain_bind', []);

			if($key = array_search($this->app->http->getName(), $bind)){
				isset($bind[$_SERVER['SERVER_NAME']]) && $domain = $_SERVER['SERVER_NAME'];

				$domain = is_bool($domain) ? $key : $domain;
			}else{
				$url = $app.'/'.($plugin ? "plugin/".$plugin."/" : "").$controller.'/'.$action;
			}
		}

		return $url;
	}

	/**
	 * 解析控制器
	 *
	 * @param string $controller
	 * @return string
	 */
	protected function parseController($controller){
		if($pos = strrpos($controller, '.')){
			$controller = substr($controller, 0, $pos).'.'.Str::snake(substr($controller, $pos + 1));
		}else{
			$controller = Str::snake($controller);
		}

		return $controller;
	}
}
