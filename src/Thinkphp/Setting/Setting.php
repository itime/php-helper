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
class Setting extends Model{
	
	/**
	 * 配置缓存键名
	 */
	const CONFIG_KEY = '__CONFIG__';
	
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
	 * 模型初始化
	 */
	protected static function init(){
		$callback = function(){
			Cache::rm(self::CONFIG_KEY);
		};
		self::afterWrite($callback);
		self::afterDelete($callback);
	}
	
	/**
	 * 更新缓存
	 */
	private static function updateCache(){
		Cache::rm(self::CONFIG_KEY);
	}
	
	/**
	 * 加载数据库设置信息
	 *
	 * @param array $settings
	 * @return array
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public static function load(array $settings = null){
		// 批量保存配置
		if(is_array($settings)){
			foreach($settings as $name => $value){
				$map = ['name' => $name];
				self::where($map)->update([
					'value' => $value,
				]);
			}
			self::updateCache();
		}
		
		$data = self::field('type,name,value')->cache(self::CONFIG_KEY)->where('status', 1)->select();
		
		$settings = [];
		foreach($data as $key => $item){
			$settings[$item['name']] = $item->value;
			unset($data[$key]);
		}
		
		return $settings;
	}
	
	/**
	 * 获取分组
	 *
	 * @return array
	 */
	public static function getGroup(){
		$groups = Config::get('web.config_group_list');
		
		if(empty($groups)){
			throw new \RuntimeException("请手动配置 settings 数据表 ‘config_group_list’标识");
		}
		
		if(!is_array($groups)){
			throw new \RuntimeException('获取配置分组数据格式异常！');
		}
		
		return $groups;
	}
	
	/**
	 * 获取扩展配置信息
	 *
	 * @param $string
	 * @return array
	 */
	protected function getExtraAttr($string){
		return self::parseValue2Array($string);
	}
	
	/**
	 * 解析配置值字符串为数字
	 *
	 * @param string $string
	 * @return array
	 */
	public final static function parseValue2Array($string){
		$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
		if(strpos($string, ':')){
			$value = [];
			foreach($array as $val){
				[$k, $v] = explode(':', $val);
				$value[$k] = $v;
			}
		}else{
			$value = $array;
		}
		
		return $value;
	}
	
	/**
	 * 获取数据类型
	 *
	 * @return string
	 * @throws \Xin\Thinkphp\Setting\NotConfigureException
	 */
	protected function getTypeTextAttr(){
		$types = Config::get('web.config_type_list');
		
		if(empty($types)){
			throw new NotConfigureException("请手动配置数据库settings数据表 ‘config_type_list’ 标识。");
		}
		
		$type = $this->getData('type');
		return isset($types[$type]) ? $types[$type] : "无";
	}
	
	/**
	 * 获取数据分组
	 *
	 * @return mixed
	 * @throws \Xin\Thinkphp\Setting\NotConfigureException
	 */
	protected function getGroupTextAttr(){
		$groups = Config::get('web.config_group_list');
		
		if(empty($groups)){
			throw new NotConfigureException(
				"请手动配置数据库settings数据表 ‘config_group_list’ 标识"
			);
		}
		
		return isset($groups[$this->group]) ? $groups[$this->group] : "无";
	}
	
	/**
	 * 根据配置类型解析配置
	 *
	 * @param mixed $val
	 * @return array
	 */
	protected function getValueAttr($val){
		$type = $this->getData('type');
		if($type == 3){
			return self::parseValue2Array($val);
		}
		return $val;
	}
	
}
