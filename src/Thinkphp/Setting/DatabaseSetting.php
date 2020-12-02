<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license       http://www.apache.org/licenses/LICENSE-2.0
 * @author        <657306123@qq.com> LXSEA
 */

namespace Xin\Thinkphp\Setting;

use think\facade\Cache;
use think\facade\Config;
use think\Model;

/**
 * 配置模型
 *
 * @property int    type
 * @property int    sort
 * @property string value
 * @property string extra
 * @property int    group
 */
class DatabaseSetting extends Model{
	
	/**
	 * 缓存数据的key
	 */
	const CACHE_KEY = 'sys_setting';
	
	/**
	 * @var string
	 */
	protected $name = 'setting';
	
	/**
	 * 禁止写入创建时间
	 *
	 * @var bool
	 */
	protected $createTime = false;
	
	/**
	 * 禁止写入更新时间
	 *
	 * @var bool
	 */
	protected $updateTime = false;
	
	/**
	 * 插入数据自动完成
	 *
	 * @var array
	 */
	protected $insert = [
		'status' => 1,
	];
	
	/**
	 * 加载数据库设置信息
	 *
	 * @param array|null $settings
	 * @return array
	 */
	public static function load(array $settings = null){
		// 批量保存配置
		if(is_array($settings)){
			foreach($settings as $name => $value){
				$map = ['name' => $name];
				self::where($map)->save([
					'value' => $value,
				]);
			}
			
			self::updateCache();
		}
		
		$config = Cache::get(self::CACHE_KEY);
		if(empty($config)){
			$config = self::updateCache();
		}
		
		return $config;
	}
	
	/**
	 * 获取分组
	 *
	 * @return array
	 * @throws \Xin\Thinkphp\Setting\InvalidConfigureException
	 */
	public static function getGroupList(){
		$groups = Config::get('web.config_group_list');
		
		if(empty($groups)){
			throw new InvalidConfigureException(
				"请手动配置 settings 数据表 ‘config_group_list’标识"
			);
		}
		
		if(!is_array($groups)){
			throw new InvalidConfigureException(
				'获取配置分组数据格式异常！'
			);
		}
		
		return $groups;
	}
	
	/**
	 * 获取数据分组
	 *
	 * @return mixed
	 * @throws \Xin\Thinkphp\Setting\InvalidConfigureException
	 */
	protected function getGroupTextAttr(){
		$groups = static::getGroupList();
		$group = $this->getData('group');
		return isset($groups[$group]) ? $groups[$group] : "无";
	}
	
	/**
	 * 获取分组
	 *
	 * @return array
	 * @throws \Xin\Thinkphp\Setting\InvalidConfigureException
	 */
	public static function getTypeList(){
		$types = Config::get('web.config_type_list');
		
		if(empty($types)){
			throw new InvalidConfigureException(
				"请手动配置数据库settings数据表 ‘config_type_list’ 标识。"
			);
		}
		
		return $types;
	}
	
	/**
	 * 获取数据类型
	 *
	 * @return string
	 * @throws \Xin\Thinkphp\Setting\InvalidConfigureException
	 */
	protected function getTypeTextAttr(){
		$types = static::getTypeList();
		$type = $this->getData('type');
		return isset($types[$type]) ? $types[$type] : "无";
	}
	
	/**
	 * 获取扩展配置信息
	 *
	 * @param $string
	 * @return array
	 */
	protected function getExtraAttr($string){
		return self::stringToArray($string);
	}
	
	/**
	 * 根据配置类型解析配置
	 *
	 * @param mixed $val
	 * @return array
	 */
	protected function getValueAttr($val){
		$type = $this->getData('type');
		
		if($type == 'array'){
			return self::stringToArray($val);
		}
		
		return $val;
	}
	
	/**
	 * 解析配置值字符串为数字
	 *
	 * @param string $string
	 * @return array
	 */
	protected static function stringToArray($string){
		$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
		
		if(strpos($string, ':')){
			$value = [];
			foreach($array as $val){
				$val = explode(':', $val);
				if(isset($val[1]) && $val[0] !== ''){
					$value[$val[0]] = $val[1];
				}else{
					$value[] = $val[0];
				}
			}
		}else{
			$value = $array;
		}
		
		return $value;
	}
	
	/**
	 * 数据写入后
	 */
	public static function onAfterWrite(){
		static::updateCache();
	}
	
	/**
	 * 数据删除后
	 */
	public static function onAfterDelete(){
		static::updateCache();
	}
	
	/**
	 * 更新缓存
	 *
	 * @return array
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public static function updateCache(){
		$data = self::field('type,name,value')
			->where('status', 1)
			->select();
		
		$settings = [];
		foreach($data as $key => $item){
			$name = explode('.', $item['name'], 2);
			$rootName = isset($name[1]) ? $name[0] : 'web';
			$name = isset($name[1]) ? $name[1] : $name[0];
			
			if(!isset($settings[$rootName])){
				$settings[$rootName] = [];
			}
			
			$settings[$rootName][$name] = $item->value;
			unset($data[$key]);
		}
		
		Cache::set(DatabaseSetting::CACHE_KEY, $settings);
		
		return $settings;
	}
}
