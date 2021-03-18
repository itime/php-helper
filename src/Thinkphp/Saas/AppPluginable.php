<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

use think\exception\ValidateException;
use think\facade\Db;
use Xin\Thinkphp\Plugin\DatabasePlugin;

/**
 * @property \think\model\Collection plugins
 * @property \think\model\Collection available_plugins
 */
trait AppPluginable{
	
	/**
	 * 关联插件模型
	 *
	 * @return \think\model\relation\BelongsToMany
	 */
	public function plugins(){
		return $this->belongsToMany(DatabasePlugin::class, DatabaseAppPlugin::class, 'plugin_id');
	}
	
	/**
	 * 检查插件是否存在
	 *
	 * @param string $name
	 * @param bool   $isAvailable
	 * @return bool
	 */
	public function hasPlugin($name, $isAvailable = true){
		return $this->hasPlugins([$name], $isAvailable);
	}
	
	/**
	 * 检查给定的一组插件是否存在
	 *
	 * @param array $names
	 * @param bool  $isAvailable
	 * @param bool  $any
	 * @return bool
	 */
	public function hasPlugins($names, $isAvailable = true, $any = true){
		if(empty($names)){
			return false;
		}
		
		$plugins = $isAvailable ? $this->getAttr('available_plugins') : $this->getPlugins();
		if($plugins->isEmpty()){
			return false;
		}
		
		$pluginNameList = $plugins->column('name');
		foreach($names as $name){
			$flag = in_array($name, $pluginNameList);
			
			if($any && $flag){
				return true;
			}elseif(!$any && !$flag){
				return false;
			}
		}
		
		return $any ? false : true;
	}
	
	/**
	 * 关联多个插件
	 *
	 * @param string $name
	 * @param int    $expireTime
	 * @param array  $options
	 * @return \think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function attachPlugin($name, $expireTime = 0, $options = []){
		return $this->attachPlugins([$name => $expireTime], $options);
	}
	
	/**
	 * 关联插件
	 *
	 * @param array $nameAndExpireTimes
	 * @param array $options
	 * @return \think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function attachPlugins($nameAndExpireTimes, $options = []){
		$names = array_keys($nameAndExpireTimes);
		
		// 检查关联的插件是否合法
		if(count(array_filter($names)) != count($names)){
			throw new ValidateException("关联的插件不合法！");
		}
		
		// 检查要关联的插件是否都存在
		$attachPlugins = DatabasePlugin::where([
			// ['status', '=', 1],
			['name', 'in', $names],
		])->select()->column(null, 'name');
		foreach($names as $name){
			if(!isset($attachPlugins[$name])){
				throw new ValidateException("关联的插件[{$name}]不存在！");
			}
		}
		
		$ownPlugins = $this->plugins()->where('app_id', $this->getOrigin('id'))->select();
		Db::transaction(function() use (&$nameAndExpireTimes, &$attachPlugins, &$ownPlugins){
			foreach($attachPlugins as $name => $plugin){
				$expireTime = $nameAndExpireTimes[$name];
				$tempPlugin = $ownPlugins->first(function($item) use ($name){
					return $item['name'] == $name;
				});
				if($tempPlugin){
					/** @var \think\model\Pivot $pivot */
					$pivot = $tempPlugin['pivot'];
					$pivot->exists(true)->save(['expire_time' => $expireTime]);
				}else{
					$pivot = new DatabaseAppPlugin([
						'plugin_id'   => $plugin->id,
						'app_id'      => $this->getOrigin('id'),
						'expire_time' => $expireTime,
					]);
					$pivot->save();
					
					$plugin->setRelation('pivot', $pivot);
					
					$ownPlugins->push($plugin);
				}
			}
		});
		
		return $ownPlugins;
	}
	
	/**
	 * 获取可用的插件列表 - 获取器
	 *
	 * @return \think\model\Collection
	 */
	protected function getAvailablePluginsAttr(){
		/** @var \think\model\Collection $ownPlugins */
		$ownPlugins = $this->plugins()->where('app_id', $this->getOrigin('id'))->select()->filter(function($item){
			$expireTime = $item['pivot']['expire_time'];
			return $expireTime !== null && ($expireTime === 0 || time() < $expireTime);
		});
		
		return $ownPlugins;
	}
	
	/**
	 * 获取插件列表
	 *
	 * @return \think\model\Collection
	 */
	private function getPlugins(){
		return $this->getAttr('plugins');
	}
}
