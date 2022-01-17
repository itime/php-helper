<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Database;

use think\Collection;
use think\Model;

class ModelUtil {

	/**
	 * 取出Pivot数据
	 * @param Collection $result
	 * @param callable   $eachCallback
	 * @return Collection
	 */
	public static function carryPivots(Collection $result, callable $eachCallback) {
		return $result->each(function (Model $model) use ($eachCallback) {
			call_user_func($eachCallback, static::carryPivot($model), $model);
		});
	}

	/**
	 * 取出Pivot数据
	 * @param Model $model
	 * @return array
	 */
	public static function carryPivot(Model $model) {
		$pivot = [];

		foreach ($model->getData() as $key => $val) {
			if (strpos($key, '__')) {
				[$name, $attr] = explode('__', $key, 2);

				if ('pivot' == $name) {
					$pivot[$attr] = $val;
					unset($model->$key);
				}
			}
		}

		return $pivot;
	}

}
