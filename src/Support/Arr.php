<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author BD<657306123@qq.com>
 */
namespace Xin\Support;

/**
 * 数组工具类
 */
final class Arr{
	
	/**
	 * 是否为关联数组
	 *
	 * @param array $arr 数组
	 * @return bool
	 */
	public static function isAssoc($arr){
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	/**
	 * 不区分大小写的in_array实现
	 *
	 * @param $value
	 * @param $array
	 * @return bool
	 */
	public static function in($value, $array){
		return in_array(strtolower($value), array_map('strtolower', $array));
	}
	
	/**
	 * 对数组排序
	 *
	 * @param array $param 排序前的数组
	 * @return array
	 */
	public static function sort(&$param){
		ksort($param);
		reset($param);
		return $param;
	}
	
	/**
	 * 除去数组中的空值和和附加键名
	 *
	 * @param array $params 要去除的数组
	 * @param array $filter 要额外过滤的数据
	 * @return array
	 */
	public static function filter(&$params, $filter = ["sign", "sign_type"]){
		foreach($params as $key => $val){
			if($val == "" || (is_array($val) && count($val) == 0)){
				unset ($params [$key]);
			}else{
				$len = count($filter);
				for($i = 0; $i < $len; $i++){
					if($key == $filter [$i]){
						unset ($params [$key]);
						array_splice($filter, $i, 1);
						break;
					}
				}
			}
		}
		return $params;
	}
	
	/**
	 * 数组栏目获取
	 *
	 * @param array  $array
	 * @param string $column
	 * @param string $index_key
	 * @return array
	 */
	public static function column(array $array, $column, $index_key = null){
		$result = [];
		foreach($array as $row){
			$key = $value = null;
			$keySet = $valueSet = false;
			if($index_key !== null && array_key_exists($index_key, $row)){
				$keySet = true;
				$key = (string)$row[$index_key];
			}
			if($column === null){
				$valueSet = true;
				$value = $row;
			}elseif(is_array($row) && array_key_exists($column, $row)){
				$valueSet = true;
				$value = $row[$column];
			}
			if($valueSet){
				if($keySet){
					$result[$key] = $value;
				}else{
					$result[] = $value;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 解包数组
	 *
	 * @param array        $array
	 * @param string|array $keys
	 * @return array
	 */
	public static function uncombine(array $array, $keys = null){
		$result = [];
		
		if($keys){
			$keys = is_array($keys) ? $keys : explode(',', $keys);
		}else{
			$keys = array_keys(current($array));
		}
		
		foreach($keys as $index => $key){
			$result[$index] = [];
		}
		
		foreach($array as $item){
			foreach($keys as $index => $key){
				$result[$index][] = isset($item[$key]) ? $item[$key] : null;
			}
		}
		
		return $result;
	}
	
	/**
	 * 数组去重-二维数组
	 * @param array  $array
	 * @param string $key
	 * @return array
	 */
	public static function multiUnique($array, $key){
		$i = 0;
		$temp_array = [];
		$key_array = [];
		
		foreach($array as $val){
			if(!in_array($val[$key], $key_array)){
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}
	
	/**
	 * 无极限分类
	 *
	 * @param array    $list 数据源
	 * @param callable $callback 额外处理回调函数
	 * @param int      $pid 父id
	 * @param string   $idName 检索对比的键名
	 * @param string   $parent 检索归属的键名
	 * @param string   $child 存放在哪？
	 * @return array
	 */
	public static function tree(array $list, callable $callback = null, $pid = 0, $idName = 'id', $parent = 'pid', $child = 'child'){
		$level = 0;
		$handler = function(array &$list, callable $callback, $pid, $idName, $parent, $child) use (&$handler, &$level){
			$level++;
			$array = [];
			foreach($list as $key => $value){
				if($value [$parent] == $pid){
					unset ($list [$key]);
					$callback($level, $value);
					
					$childList = $handler($list, $callback, $value [$idName], $idName, $parent, $child);
					if(!empty($childList)) $value [$child] = $childList;
					
					$array [] = $value;
					reset($list);
				}
			}
			$level--;
			return $array;
		};
		
		is_null($callback) && $callback = function(){ };
		return $handler($list, $callback, $pid, $idName, $parent, $child);
	}
	
	/**
	 * 树转tree
	 *
	 * @param array  $list
	 * @param string $child
	 * @return array
	 */
	public static function treeToList($list, $child = 'child'){
		$handler = function($list, $child) use (&$handler){
			$result = [];
			foreach($list as $key => &$val){
				$result[] = &$val;
				unset($list[$key]);
				if(isset($val[$child])){
					$result = array_merge($result, $handler($val[$child], $child));
					unset($val[$child]);
				}
			}
			unset($val);
			return $result;
		};
		return $handler($list, $child);
	}
	
	/**
	 * 转换数组里面的key
	 *
	 * @param array $arr
	 * @param array $keyMaps
	 * @return array
	 */
	public static function transformKeys(array $arr, array $keyMaps){
		foreach($keyMaps as $oldKey => $newKey){
			if(!array_key_exists($oldKey, $arr)) continue;
			
			if(is_callable($newKey)){
				[$newKey, $value] = call_user_func($newKey, $arr[$oldKey], $oldKey, $arr);
				$arr[$newKey] = $value;
			}else{
				$arr[$newKey] = $arr[$oldKey];
			}
			unset($arr[$oldKey]);
		}
		return $arr;
	}
	
	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @param array $array
	 * @param int   $depth
	 * @return array
	 */
	public static function flatten($array, $depth = INF){
		$result = [];
		
		foreach($array as $item){
			if(!is_array($item)){
				$result[] = $item;
			}else{
				$values = $depth === 1
					? array_values($item)
					: static::flatten($item, $depth - 1);
				
				foreach($values as $value){
					$result[] = $value;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|int         $key
	 * @return bool
	 */
	public static function exists($array, $key){
		if($array instanceof \ArrayAccess){
			return $array->offsetExists($key);
		}
		
		return array_key_exists($key, $array);
	}
	
	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|int         $key
	 * @param mixed              $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null){
		if(!static::accessible($array)){
			return value($default);
		}
		
		if(is_null($key)){
			return $array;
		}
		
		if(static::exists($array, $key)){
			return $array[$key];
		}
		
		if(strpos($key, '.') === false){
			return isset($array[$key]) ? $array[$key] : value($default);
		}
		
		foreach(explode('.', $key) as $segment){
			if(static::accessible($array) && static::exists($array, $segment)){
				$array = $array[$segment];
			}else{
				return value($default);
			}
		}
		
		return $array;
	}
	
	/**
	 * Check if an item or items exist in an array using "dot" notation.
	 *
	 * @param \ArrayAccess|array $array
	 * @param string|array       $keys
	 * @return bool
	 */
	public static function has($array, $keys){
		$keys = (array)$keys;
		
		if(!$array || $keys === []){
			return false;
		}
		
		foreach($keys as $key){
			$subKeyArray = $array;
			
			if(static::exists($array, $key)){
				continue;
			}
			
			foreach(explode('.', $key) as $segment){
				if(static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)){
					$subKeyArray = $subKeyArray[$segment];
				}else{
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Set an array item to a given value using "dot" notation.
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @param array  $array
	 * @param string $key
	 * @param mixed  $value
	 * @return array
	 */
	public static function set(&$array, $key, $value){
		if(is_null($key)){
			return $array = $value;
		}
		
		$keys = explode('.', $key);
		
		while(count($keys) > 1){
			$key = array_shift($keys);
			
			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if(!isset($array[$key]) || !is_array($array[$key])){
				$array[$key] = [];
			}
			
			$array = &$array[$key];
		}
		
		$array[array_shift($keys)] = $value;
		
		return $array;
	}
	
	/**
	 * 从数组里面获取指定的数据
	 *
	 * @param array $data
	 * @param array $keys
	 * @return array
	 */
	public static function only($data, array $keys){
		$result = [];
		foreach($keys as $key){
			if(isset($data[$key])){
				$result[$key] = $data[$key];
			}
		}
		
		return $result;
	}
	
	/**
	 * Get one or a specified number of random values from an array.
	 *
	 * @param array    $array
	 * @param int|null $number
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public static function random($array, $number = null){
		$requested = is_null($number) ? 1 : $number;
		
		$count = count($array);
		
		if($requested > $count){
			throw new \InvalidArgumentException(
				"You requested {$requested} items, but there are only {$count} items available."
			);
		}
		
		if(is_null($number)){
			return $array[array_rand($array)];
		}
		
		if((int)$number === 0){
			return [];
		}
		
		$keys = array_rand($array, $number);
		
		$results = [];
		
		foreach((array)$keys as $key){
			$results[] = $array[$key];
		}
		
		return $results;
	}
	
	/**
	 * Shuffle the given array and return the result.
	 *
	 * @param array    $array
	 * @param int|null $seed
	 * @return array
	 */
	public static function shuffle($array, $seed = null){
		if(is_null($seed)){
			shuffle($array);
		}else{
			mt_srand($seed);
			shuffle($array);
			mt_srand();
		}
		
		return $array;
	}
	
	/**
	 * If the given value is not an array and not null, wrap it in one.
	 *
	 * @param mixed $value
	 * @return array
	 */
	public static function wrap($value){
		if(is_null($value)){
			return [];
		}
		
		return is_array($value) ? $value : [$value];
	}
	
	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @param array  $array
	 * @param string $prepend
	 * @return array
	 */
	public static function dot($array, $prepend = ''){
		$results = [];
		
		foreach($array as $key => $value){
			if(is_array($value) && !empty($value)){
				$results = array_merge($results, static::dot($value, $prepend.$key.'.'));
			}else{
				$results[$prepend.$key] = $value;
			}
		}
		
		return $results;
	}
	
	/**
	 * Determine whether the given value is array accessible.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function accessible($value){
		return is_array($value) || $value instanceof \ArrayAccess;
	}
}
