<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Model;

/**
 * @method $this app(int $appId)
 * @mixin \think\Model
 */
trait Appable
{

	/**
	 * 开启 app_id 全局约束
	 *
	 * @return $this
	 */
	public function withGlobalAppScope()
	{
		if (!in_array('app', $this->globalScope)) {
			$this->globalScope[] = 'app';
		}

		return $this;
	}

	/**
	 * 关闭 app_id 全局约束
	 *
	 * @return $this
	 */
	public function withoutGlobalAppScope()
	{
		$index = array_search('app_id', $this->globalScope);

		if ($index !== false) {
			array_splice($this->globalScope, $index, 1);
		}

		return $this;
	}

	/**
	 * App 作用域
	 *
	 * @param \think\db\Query $query
	 * @param int $appId
	 * @hidden
	 */
	public function scopeApp($query, $appId = 0)
	{
		if (!$appId) {
			$appId = AppContext::getInstance()->getGlobalAppId();
		}

		$query->where('app_id', $appId);
	}

	/**
	 * @param \think\Model $model
	 */
	protected static function onBeforeWrite($model)
	{
		$appId = AppContext::getInstance()->getGlobalAppId();
		if ($appId !== null) {
			$model['app_id'] = $appId;
		}
	}

}
