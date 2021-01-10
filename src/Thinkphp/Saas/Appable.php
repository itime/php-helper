<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

/**
 * @method $this app(int $appId)
 * @mixin \think\Model
 */
trait Appable{
	
	/**
	 * @var int
	 */
	protected static $globalAppId = null;
	
	/**
	 * 开启 app_id 全局约束
	 *
	 * @return $this
	 */
	public function withGlobalAppScope(){
		if(!in_array('app', $this->globalScope)){
			$this->globalScope[] = 'app';
		}
		
		return $this;
	}
	
	/**
	 * 关闭 app_id 全局约束
	 *
	 * @return $this
	 */
	public function withoutGlobalAppScope(){
		$index = array_search('app_id', $this->globalScope);
		
		if($index !== false){
			array_splice($this->globalScope, $index, 1);
		}
		
		return $this;
	}
	
	/**
	 * App 作用域
	 *
	 * @param \think\db\Query $query
	 * @param int             $appId
	 * @hidden
	 */
	public function scopeApp($query, $appId = 0){
		if(!$appId){
			$appId = static::$globalAppId;
		}
		
		$query->where('app_id', $appId);
	}
	
	/**
	 * 启用 App
	 *
	 * @param int $appId
	 */
	public static function enableApp($appId){
		static::$globalAppId = $appId;
		
		static::maker(function(/**@var Appable $model */ $model){
			if(static::$globalAppId !== null){
				$model->withGlobalAppScope();
			}
		});
	}
	
	/**
	 * @param \think\Model $model
	 */
	protected static function onBeforeWrite($model){
		if(static::$globalAppId !== null){
			$model['app_id'] = static::$globalAppId;
		}
	}
	
}
