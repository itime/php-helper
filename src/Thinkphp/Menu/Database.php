<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Menu;

use think\exception\ClassNotFoundException;
use Xin\Menu\Driver;
use Xin\Support\Arr;

class Database extends Driver{

	/**
	 * @var array
	 */
	protected $data = null;

	/**
	 * 加载数据
	 */
	protected function load($app = null){
		if($this->data === null){
			$this->data = $this->model()->select()->all();
		}

		if($app === null){
			return [];
		}

		$result = [];
		foreach($this->data as &$item){
			if($item['app'] == $app){
				$result[] = &$item;
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function all(){
		$this->load();
		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function get($user){
		$this->load();
		return Arr::tree($this->data);
	}

	/**
	 * 根据一组菜单进行查找某个菜单
	 *
	 * @param array  $menus
	 * @param string $url
	 * @param string $app
	 * @return \think\Model
	 */
	protected function &find($menus, $url, $app){
		$default = null;

		foreach($menus as &$item){
			if(Arr::where($item, [
				'app' => $app,
				'url' => $url,
			])){
				return $item;
			}
		}

		return $default;
	}

	/**
	 * @inheritDoc
	 */
	public function puts($menus, $plugin = null, $append = []){
		$plugin = empty($plugin) ? '' : $plugin;

		$originAppMenus = $this->load($plugin);
		$updatedMenuIdList = [];
		self::eachTree(function(&$item, &$parent) use (&$append, $plugin, &$originAppMenus, &$updatedMenuIdList){
			$item = [
				'title'      => $item['title'],
				'url'        => $item['url'] ?? $menu['name'] ?? '',
				'show'       => $item['show'] ?? 0,
				'only_admin' => $item['only_admin'] ?? 0,
				'only_dev'   => $item['only_dev'] ?? 0,
				'sort'       => $item['sort'] ?? 0,
				'pid'        => $parent ? $parent['id'] : 0,
				'icon'       => $menu['icon'] ?? '',
				'app'        => empty($plugin) ? '' : $plugin,
			];

			$findMenu = $this->find($originAppMenus, $item['url'], $plugin);
			if(empty($findMenu)){
				$data = array_merge($item, $append);
				$model = $this->model($data);
				$model->save();
				$this->data[] = $model;

				$item['id'] = $model['id'];
			}else{
				$findMenu->data([
					'show'       => $item['show'],
					'only_admin' => $item['only_admin'],
					'only_dev'   => $item['only_dev'],
					'sort'       => $item['sort'],
					'pid'        => $item['pid'],
					'icon'       => $item['icon'],
				]);
				$findMenu->save();

				$updatedMenuIdList[] = $findMenu['id'];
			}
		}, $menus);

		// 删除不存在的菜单
		$deleteMenuIdList = [];
		foreach($originAppMenus as $menu){
			if(!in_array($menu['id'], $deleteMenuIdList)){
				$deleteMenuIdList = $menu['id'];
			}
		}
		if(empty($deleteMenuIdList)){
			$this->forget([
				'id', 'in', $deleteMenuIdList,
			]);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function forget($condition){
		$this->model()->where($condition)->delete();

		foreach($this->data as $key => &$menu){
			if(Arr::where($menu, $condition) === true){
				unset($this->data[$key]);
			}
		}
		unset($menu);

		return true;
	}

	/**
	 * 获取模型实例
	 *
	 * @param array $data
	 * @return \think\Model
	 */
	protected function model(array $data = []){
		$modelClass = $this->modelClass();
		return new $modelClass($data);
	}

	/**
	 * 获取模型类
	 *
	 * @return string
	 */
	protected function modelClass(){
		$class = $this->config('model');

		if(!class_exists($class)){
			throw new ClassNotFoundException("[$class] not found!");
		}

		return $class;
	}

	public function refresh($plugin = null){
		// TODO: Implement refresh() method.
	}
}
