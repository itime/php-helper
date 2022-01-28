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

class Database extends Driver
{

	/**
	 * @var \think\Collection
	 */
	protected $data = null;

	/**
	 * 加载数据
	 *
	 * @param string $plugin
	 * @return array|\think\Collection|\think\Model[]
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function load($plugin = null)
	{
		if ($this->data === null) {
			$this->data = $this->model()->order('sort')->select();
		}

		if ($plugin === null) {
			return $this->data;
		}

		return $this->data->filter(function ($item) use ($plugin) {
			return $item['plugin'] == $plugin;
		});
	}

	/**
	 * @inheritDoc
	 */
	public function all()
	{
		$this->load();

		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function get($filter = null)
	{
		$this->load();

		if ($filter) {
			return Arr::tree($this->data->filter($filter)->toArray());
		}

		return Arr::tree($this->data->toArray());
	}

	/**
	 * 根据一组菜单进行查找某个菜单
	 *
	 * @param array $menus
	 * @param string $url
	 * @param string $plugin
	 * @return \think\Model
	 */
	protected function &find($menus, $url, $plugin)
	{
		$default = null;

		foreach ($menus as &$item) {
			if (Arr::where($item, [
				'plugin' => $plugin,
				'url' => $url,
			])) {
				return $item;
			}
		}

		return $default;
	}

	/**
	 * 查找父级ID
	 *
	 * @param array $current
	 * @param array $parent
	 * @return int
	 */
	protected function findParentId($current, $parent)
	{
		if (isset($current['parent'])) {
			foreach ($this->data as $item) {
				$name = $item['name'] ?? $item['url'] ?? '';
				if ($name == $current['parent']) {
					return $item['id'] ?? 0;
				}
			}

			return 0;
		}

		return $parent ? $parent['id'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function puts($menus, $plugin = null, $append = [])
	{
		$plugin = empty($plugin) ? '' : $plugin;

		$addedMenuIdList = [];
		$updatedMenuIdList = [];
		$originAppMenus = $this->load($plugin);
		self::eachTree(function (&$item, &$parent) use (&$append, $plugin, &$originAppMenus, &$addedMenuIdList, &$updatedMenuIdList) {
			$saveItem = array_merge([
				'title' => $item['title'],
				'url' => $item['url'] ?? $menu['name'] ?? '',
				'show' => $item['show'] ?? 0,
				'only_admin' => $item['only_admin'] ?? 0,
				'only_dev' => $item['only_dev'] ?? 0,
				'link' => $item['link'] ?? (isset($item['child']) ? 0 : 1) ?? 1,
				'sort' => $item['sort'] ?? 0,
				'pid' => $this->findParentId($item, $parent),
				'icon' => $item['icon'] ?? '',
				'plugin' => empty($plugin) ? '' : $plugin,
				'system' => 1,
			], $append);

			$menuModel = $this->find($originAppMenus, $saveItem['url'], $plugin);
			if (empty($menuModel)) {
				$menuModel = $this->model($saveItem);
				$menuModel->save();

				$addedMenuIdList[] = $menuModel['id'];

				$this->data[] = $menuModel;
			} else {
				$menuModel->save([
					'only_admin' => $saveItem['only_admin'],
					'only_dev' => $saveItem['only_dev'],
					'link' => $saveItem['link'],
					'sort' => $saveItem['sort'],
					'icon' => $saveItem['icon'],
					'system' => 1,
				]);

				$updatedMenuIdList[] = $menuModel['id'];
			}

			$item['id'] = $menuModel['id'];
		}, $menus);

		// 删除不存在的菜单
		$deleteMenuIdList = [];
		foreach ($originAppMenus as $menu) {
			if (!in_array($menu['id'], $updatedMenuIdList)) {
				$deleteMenuIdList[] = $menu['id'];
			}
		}
		if (!empty($deleteMenuIdList)) {
			$this->forget([
				['id', 'in', $deleteMenuIdList,],
			]);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function forget($condition)
	{
		$this->model()->where($condition)->delete();

		foreach ($this->data as $key => &$menu) {
			if (Arr::where($menu, $condition) === true) {
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
	protected function model(array $data = [])
	{
		$modelClass = $this->modelClass();

		return new $modelClass($data);
	}

	/**
	 * 获取模型类
	 *
	 * @return string
	 */
	protected function modelClass()
	{
		$class = $this->config('model');

		if (!class_exists($class)) {
			throw new ClassNotFoundException("[$class] not found!");
		}

		return $class;
	}

	public function refresh($plugin = null)
	{
		// TODO: Implement refresh() method.
	}

}
