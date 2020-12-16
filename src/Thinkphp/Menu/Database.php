<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Menu;

use app\admin\model\AdminMenu;
use Xin\Menu\Driver;

class Database extends Driver{
	
	/**
	 * @inheritDoc
	 */
	public function puts($menus, $append = []){
		self::each(function($item, $parent) use ($append){
			return $this->insertDb($item, $parent ? $parent['id'] : 0, $append);
		}, $menus);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function insertDb($menu, $pid = 0, $append = []){
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
		
		return AdminMenu::create($data);
	}
	
	/**
	 * @inheritDoc
	 */
	public function forget($name){
		// TODO: Implement forget() method.
	}
}
