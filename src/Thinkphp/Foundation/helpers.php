<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

use Xin\Support\Str;

if(!function_exists('listen')){
	/**
	 * 监听行为
	 *
	 * @param string     $event
	 * @param array|null $params
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
		if(strpos($url, '\\') === false){
			$info = explode("/", $url, 2);
			$controller = array_pop($info);
			$controller = str_replace(".", "/", $controller);
			$appName = array_pop($info) ?: app()->http->getName();
			$suffix = $appendSuffix ? Str::studly($layer) : '';
			$class = "app\\{$appName}\\{$layer}\\{$controller}{$suffix}";
		}else{
			$class = $url;
		}

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
		$actionIndex = strrpos($url, "/");
		if(!$actionIndex || empty($action = substr($url, $actionIndex + 1))){
			throw new \LogicException("url parse action no exist.");
		}

		$controller = substr($url, 0, $actionIndex);
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
		return action($url."/handle", $vars, 'weight', $appendSuffix);
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

if(!function_exists('db')){
	/**
	 * 获取 db 实例
	 *
	 * @param string $name
	 * @return \think\db\Query
	 */
	function db($name){
		return app('db')->name($name);
	}
}

if(!function_exists('db_rows')){
	/**
	 * 获取数据库数据
	 *
	 * @param string $table
	 * @param array  $where
	 * @param string $field
	 * @param string $order
	 * @param string $page
	 * @return array|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	function db_rows($table, $where = [], $field = '*', $order = '', $page = ''){
		return db($table)->field($field)->where($where)->order($order)->page(intval($page))->select();
	}
}

if(!function_exists('db_columns')){
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
	function db_columns($table, $field, $where = [], $key = '', $order = '', $page = ''){
		return db($table)->where($where)->order($order)->page(intval($page))->column($field, $key);
	}
}

if(!function_exists('db_value')){
	/**
	 * 获取数据库值
	 *
	 * @param string       $table
	 * @param string       $field 字段名
	 * @param array|string $where
	 * @param mixed        $default 默认值
	 * @return mixed
	 */
	function db_value($table, $field, $where, $default = null){
		return db($table)->where($where)->value($field, $default);
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
if(!function_exists('decrypt')){
	/**
	 * Decrypt the given value.
	 *
	 * @param string $value
	 * @param bool   $unserialize
	 * @return mixed
	 */
	function decrypt($value, $unserialize = true){
		return app('encrypter')->decrypt($value, $unserialize);
	}
}

if(!function_exists('encrypt')){
	/**
	 * Encrypt the given value.
	 *
	 * @param mixed $value
	 * @param bool  $serialize
	 * @return string
	 */
	function encrypt($value, $serialize = true){
		return app('encrypter')->encrypt($value, $serialize);
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

