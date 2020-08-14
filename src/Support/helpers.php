<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

use Illuminate\Support\HigherOrderTapProxy;

if(!function_exists('value')){
	/**
	 * Return the default value of the given value.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	function value($value){
		return $value instanceof Closure ? $value() : $value;
	}
}

if(!function_exists('windows_os')){
	/**
	 * Determine whether the current environment is Windows based.
	 *
	 * @return bool
	 */
	function windows_os(){
		return strtolower(substr(PHP_OS, 0, 3)) === 'win';
	}
}

if(!function_exists('blank')){
	/**
	 * Determine if the given value is "blank".
	 *
	 * @param mixed $value
	 * @return bool
	 */
	function blank($value){
		if(is_null($value)){
			return true;
		}
		
		if(is_string($value)){
			return trim($value) === '';
		}
		
		if(is_numeric($value) || is_bool($value)){
			return false;
		}
		
		if($value instanceof Countable){
			return count($value) === 0;
		}
		
		return empty($value);
	}
}

if(!function_exists('object_get')){
	/**
	 * Get an item from an object using "dot" notation.
	 *
	 * @param object $object
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	function object_get($object, $key, $default = null){
		if(is_null($key) || trim($key) == ''){
			return $object;
		}
		
		foreach(explode('.', $key) as $segment){
			if(!is_object($object) || !isset($object->{$segment})){
				return value($default);
			}
			
			$object = $object->{$segment};
		}
		
		return $object;
	}
}

if(!function_exists('tap')){
	/**
	 * Call the given Closure with the given value then return the value.
	 *
	 * @param mixed         $value
	 * @param callable|null $callback
	 * @return mixed
	 */
	function tap($value, $callback = null){
		if(is_null($callback)){
			return new HigherOrderTapProxy($value);
		}
		
		$callback($value);
		
		return $value;
	}
}
