<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\facade\Cache;
use think\Model;

/**
 * @property array addons
 * @property int   status
 */
class DatabaseEvent extends Model{
	
	/**
	 * @var string
	 */
	protected $name = 'event';
	
	/**
	 * @var string[]
	 */
	protected $schema = [
		'id'          => 'int',
		'name'        => 'string',
		'description' => 'string',
		'type'        => 'int',
		'addons'      => 'string',
		'system'      => 'int',
		'status'      => 'int',
		'update_time' => 'int',
		'create_time' => 'int',
	];
	
	/**
	 * 缓存前缀
	 */
	const CACHE_PREFIX = 'event:';
	
	/**
	 * @var array
	 */
	protected static $TYPE_TEXT_LIST = [
		'0' => '视图',
		'1' => '控制器',
	];
	
	/**
	 * 获取类型说明
	 *
	 * @return string
	 */
	public function getTypeTextAttr(){
		$type = $this->getData('type');
		return self::$TYPE_TEXT_LIST[$type] ?? '';
	}
	
	/**
	 * 获取附件列表
	 *
	 * @param string $addons
	 * @return array
	 */
	public function getAddonsAttr($addons){
		return empty($addons) ? [] : explode(",", $addons);
	}
	
	/**
	 * 设置附件列表
	 *
	 * @param array $addons
	 * @return string
	 */
	public function setAddonsAttr($addons){
		return implode(",", $addons);
	}
	
	/**
	 * 数据写入之后
	 *
	 * @param static $event
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function onAfterWrite(self $event){
		if(isset($event->id)){
			self::setCache($event);
		}else{
			$events = $event->where($event->getWhere())->select();
			foreach($events as $event){
				self::setCache($event);
			}
		}
	}
	
	/**
	 * 数据删除之后
	 *
	 * @param static $event
	 */
	public static function onAfterDelete(self $event){
		Cache::delete(self::CACHE_PREFIX.$event->name);
	}
	
	/**
	 * 获取事件列表
	 *
	 * @param array|null $names
	 * @return \think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected static function resolveList($names){
		if($names === null){
			$eventCollection = static::select();
		}else{
			$names = is_array($names) ? $names : [$names];
			$eventCollection = static::where('name', 'in', $names)->select();
		}
		
		return $eventCollection;
	}
	
	/**
	 * 挂载插件
	 *
	 * @param string     $addon
	 * @param array|null $names
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function mountAddon($addon, $names){
		$eventCollection = static::resolveList($names);
		
		/** @var static $event */
		foreach($eventCollection as $event){
			$addons = $event->addons;
			if(!in_array($addon, $addons)){
				$addons[] = $addon;
				$addons = array_filter($addons);
				$event->addons = $addons;
				$event->save();
			}
		}
	}
	
	/**
	 * 取消挂载插件
	 *
	 * @param array|null $names
	 * @param string     $addon
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function unmountAddon($addon, $names = null){
		$eventCollection = static::resolveList($names);
		
		/** @var static $event */
		foreach($eventCollection as $event){
			$addons = $event->addons;
			if(in_array($addon, $addons)){
				$addons = array_filter($addons, function($it) use ($addon){
					return !empty($it) && $it != $addon;
				});
				$event->addons = $addons;
				$event->save();
			}
		}
	}
	
	/**
	 * 自动更新事件缓存
	 */
	public static function autoUpdateCache(){
		if(Cache::get(self::CACHE_PREFIX)){
			return;
		}
		
		self::updateCache();
		
		Cache::set(self::CACHE_PREFIX, 1);
	}
	
	/**
	 * 更新数据缓存
	 *
	 * @return \think\Collection
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public static function updateCache(){
		$eventCollection = static::where('status', 1)->select();
		
		/** @var static $event */
		foreach($eventCollection as $event){
			self::setCache($event);
		}
		
		return $eventCollection;
	}
	
	/**
	 * 设置事件缓存
	 *
	 * @param static $event
	 */
	public static function setCache(self $event){
		$addons = $event->status ? $event->addons : [];
		$cacheKey = self::CACHE_PREFIX.$event->getData('name');
		
		Cache::set($cacheKey, [
			'type'   => $event->getData('type'),
			'addons' => $addons,
		]);
	}
	
	/**
	 * 获取缓存数据
	 *
	 * @param string $name
	 * @return array
	 */
	public static function getCache($name){
		return Cache::get(self::CACHE_PREFIX.$name);
	}
	
	/**
	 * 根据事件类型获取事件后缀
	 *
	 * @param int $type
	 * @return string
	 */
	public static function getLayerOfType($type){
		switch($type){
			case 0:
				return "weight";
			case 1:
				return "event";
		}
		return "";
	}
	
}
