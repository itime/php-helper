<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

/**
 * @method $this app(int $appId)
 */
trait App{
	
	/**
	 * App 作用域
	 *
	 * @param \think\db\Query $query
	 * @param int             $appId
	 */
	public function scopeApp($query, $appId){
		if(!$appId){
			$appId = request()->appId();
		}
		
		$query->where('app_id', $appId);
	}
	
	
}
