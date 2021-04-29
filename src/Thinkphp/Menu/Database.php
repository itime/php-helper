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
	 * @inheritDoc
	 */
	public function all(){
		return $this->model()->select();
	}

	/**
	 * @inheritDoc
	 */
	public function get($user){
		return Arr::tree($this->model()->select()->toArray());
	}

	/**
	 * @inheritDoc
	 */
	public function puts($menus, $append = []){
		self::eachTree(function(&$item, &$parent) use ($append){
			$id = $this->insert($item, $parent ? $parent['id'] : 0, $append);
			$item['id'] = $id;
		}, $menus);
	}

	/**
	 * 插入一个菜单
	 *
	 * @param array $menu
	 * @param int   $pid
	 * @param array $append
	 * @return bool
	 */
	protected function insert($menu, $pid = 0, $append = []){
		$data = [
			'title'      => $menu['title'],
			'url'        => $menu['url'] ?? $menu['name'] ?? '',
			'show'       => $menu['show'] ?? 0,
			'only_admin' => $menu['only_admin'] ?? 0,
			'only_dev'   => $menu['only_dev'] ?? 0,
			'sort'       => $menu['sort'] ?? 0,
			'pid'        => $pid,
			'icon'       => $menu['icon'] ?? '',
		];
		$data = array_merge($data, $append);

		$model = $this->model($data);
		$model->save();

		return $model['id'];
	}

	/**
	 * @inheritDoc
	 */
	public function forget($condition){
		return $this->model()->where($condition)->delete();
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
}
