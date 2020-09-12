<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

use Xin\Support\Str;

if(!function_exists('thinkphp_version')){
	/**
	 * ThinkPHP 版本
	 *
	 * @return string
	 */
	function thinkphp_version(){
		static $version = null;
		
		if(empty($version)){
			if(const_exist('\think\App', 'VERSION')){
				$version = "5.1";
			}
			
			$version = "6.0";
		}
		
		return $version;
	}
}

if(!function_exists('thinkphp_is')){
	/**
	 * ThinkPHP版本号是否是指定版本
	 *
	 * @param string $version
	 * @return bool
	 */
	function thinkphp_is($version){
		$tVersion = thinkphp_version();
		return $version == $tVersion;
	}
}

if(!function_exists('thinkphp51_if')){
	/**
	 * 如果ThinkPHP版本是5.1执行第一个回调，如果不是执行第二个回调
	 *
	 * @param callable $resolve
	 * @param callable $reject
	 * @return mixed
	 */
	function thinkphp51_if($resolve, $reject = null){
		if(thinkphp_is("5.1")){
			return call_user_func($resolve);
		}elseif($reject){
			return call_user_func($reject);
		}
		
		return null;
	}
}

if(!function_exists('thinkphp60_if')){
	/**
	 * 如果ThinkPHP版本是6.0执行第一个回调，如果不是执行第二个回调
	 *
	 * @param callable $resolve
	 * @param callable $reject
	 * @return mixed|null
	 */
	function thinkphp60_if($resolve, $reject = null){
		if(thinkphp_is("6.0")){
			return call_user_func($resolve);
		}elseif($reject){
			return call_user_func($reject);
		}
		
		return null;
	}
}

if(!function_exists('event')){
	/**
	 * 监听行为
	 *
	 * @param string $event
	 * @param array  $params
	 */
	function listen($event, $params = null){
		if(is_object($event)){
			$params = $event;
			$event = get_class($event);
		}
		
		app('hook')->listen($event, $params);
	}
}

if(!function_exists('call')){
	/**
	 * 调用反射执行callable 支持参数绑定
	 *
	 * @access public
	 * @param mixed $callable
	 * @param array $vars 参数
	 * @param bool  $accessible 设置是否可访问
	 * @return mixed
	 */
	function call($callable, array $vars = [], bool $accessible = false){
		return app()->invoke($callable, $vars, $accessible);
	}
}

if(!function_exists('controller')){
	/**
	 * 实例化控制器
	 *
	 * @param string $url
	 * @param string $layer
	 * @param bool   $appendSuffix
	 * @return mixed
	 */
	function controller($url, $layer = 'controller', $appendSuffix = true){
		$info = explode("/", $url, 2);
		$controller = array_pop($info);
		$controller = str_replace(".", "/", $controller);
		$appName = array_pop($info) ?: app()->http->getName();
		
		$suffix = $appendSuffix ? Str::studly($layer) : '';
		$class = "app\\{$appName}\\{$layer}\\{$controller}{$suffix}";
		
		return app($class);
	}
}

if(!function_exists('action')){
	/**
	 * 调用操作
	 *
	 * @param string $url
	 * @param array  $vars
	 * @param string $layer
	 * @param bool   $appendSuffix
	 * @return mixed
	 */
	function action($url, array $vars = [], $layer = 'controller', $appendSuffix = true){
		$info = explode("/", $url, 2);
		$action = array_pop($info);
		
		$controller = array_pop($info);
		$controller = controller($controller, $layer, $appendSuffix);
		
		return call([$controller, $action], $vars);
	}
}

if(!function_exists('weight')){
	/**
	 * 执行小挂件
	 *
	 * @param string $url
	 * @param array  $vars
	 * @param bool   $appendSuffix
	 * @return mixed
	 */
	function weight($url, $vars = [], $appendSuffix = false){
		return action($url, $vars, 'weight', $appendSuffix);
	}
}

if(!function_exists('logic')){
	/**
	 * 获取业务控制器
	 *
	 * @param string $name
	 * @param bool   $appendSuffix
	 * @return mixed
	 */
	function logic($name, $appendSuffix = true){
		return controller($name, 'logic', $appendSuffix);
	}
}

if(!function_exists('logic_action')){
	/**
	 * 执行业务控制器的方法
	 *
	 * @param string $name
	 * @param array  $vars
	 * @param bool   $appendSuffix
	 * @return mixed
	 */
	function logic_action($name, $vars = [], $appendSuffix = true){
		return action($name, $vars, 'logic', $appendSuffix);
	}
}

if(!function_exists('table_rows')){
	/**
	 * 获取数据库数据
	 *
	 * @param string $table
	 * @param array  $where
	 * @param string $field
	 * @param string $order
	 * @param string $page
	 * @return array|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	function table_rows($table, $where = [], $field = '*', $order = '', $page = ''){
		return \think\Db::name($table)->field($field)->where($where)->order($order)->page($page)->select();
	}
}

if(!function_exists('table_column')){
	/**
	 * 获取数据库数据
	 *
	 * @param string $table
	 * @param string $field
	 * @param array  $where
	 * @param string $key
	 * @param string $order
	 * @param string $page
	 * @return array
	 */
	function table_column($table, $field, $where = [], $key = '', $order = '', $page = ''){
		return \think\Db::name($table)->where($where)->order($order)->page($page)->column($field, $key);
	}
}

if(!function_exists('table_value')){
	/**
	 * 获取数据库值
	 *
	 * @param string       $table
	 * @param string       $field 字段名
	 * @param array|string $where
	 * @param mixed        $default 默认值
	 * @return mixed
	 */
	function table_value($table, $field, $where, $default = null){
		return \think\Db::name($table)->where($where)->value($field, $default);
	}
}

if(!function_exists('bcrypt')){
	/**
	 * Hash the given value against the bcrypt algorithm.
	 *
	 * @param string $value
	 * @param array  $options
	 * @return string
	 */
	function bcrypt($value, $options = []){
		return app('hash')->make($value, $options);
	}
}

if(!function_exists('get_cover_path')){
	/**
	 * 获取图片地址
	 *
	 * @param string $path
	 * @return mixed
	 */
	function get_cover_path($path){
		if(strpos($path, '/') === 0){
			return request()->domain().$path;
		}
		return $path;
	}
}

if(!function_exists('optimize_asset')){
	/**
	 * 优化资源路径
	 *
	 * @param string $uri
	 * @param bool   $prefix
	 * @return string
	 */
	function optimize_asset($uri, $prefix = false){
		$index = strpos($uri, '://');
		if($index === false){
			$uri = "//".request()->host().$uri;
		}elseif(0 !== $index){
			$uri = substr($uri, $index + 1);
		}
		
		if($prefix){
			if(is_bool($prefix)){
				$uri = 'http:'.$uri;
			}else{
				$uri = $prefix.':'.$uri;
			}
		}
		
		return $uri;
	}
}

if(!function_exists('analysis_words')){
	/**
	 * 关键字分词
	 *
	 * @param string $keyword
	 * @param int    $num 最大返回条数
	 * @param int    $holdLength 保留字数
	 * @return array
	 */
	function analysis_words($keyword, $num = 5, $holdLength = 48){
		if($keyword === null || $keyword === "") return [];
		if(mb_strlen($keyword) > $holdLength) $keyword = mb_substr($keyword, 0, 48);
		
		//执行分词
		$pa = new \Xin\Analysis\Analysis('utf-8', 'utf-8');
		$pa->setSource($keyword);
		$pa->startAnalysis();
		$result = $pa->getFinallyResult($num);
		if(empty($result)) return [$keyword];
		
		return array_unique($result);
	}
}

