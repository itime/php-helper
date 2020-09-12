<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Contracts\Menu\Menu;

class ArrayMenu implements Menu{
	
	/**
	 * @var array
	 */
	protected $menus = [];
	
	/**
	 * @var array
	 */
	protected $breads = [];
	
	/**
	 * @var array
	 */
	protected $query = [];
	
	/**
	 * @inheritDoc
	 */
	public function generate(array $options = []){
		$this->query = isset($options['query']) ? $options['query'] : [];
		$this->menus = isset($options['menus']) ? $options['menus'] : [];
		
		$this->tree($options['rule'], $this->menus);
		
		$this->breads = array_reverse($this->breads);
		array_pop($this->breads);
		//		$this->breads = Arr::multiUnique($this->breads, 'name');
		
		return [
			$this->menus,
			$this->breads,
		];
	}
	
	/**
	 * @param string $rule
	 * @param array  $menus
	 * @return bool
	 */
	protected function tree($rule, &$menus){
		$isActive = false;
		
		foreach($menus as &$menu){
			$menu['active'] = false;
			
			if(isset($menu['show'])){
				$menu['show'] = value($menu['show']);
			}else{
				$menu['show'] = false;
			}
			
			if(isset($menu['child']) && $this->tree($rule, $menu['child'])){
				$menu['active'] = $isActive = true;
			}elseif($menu['name'] != ''){
				$menuRule = explode("?", $menu['name'], 2);
				if($menuRule[0] != $rule){
					continue;
				}
				
				// 校验参数
				if(!isset($menuRule[1]) || $this->checkQueryParams($menuRule[1])){
					$menu['active'] = $isActive = true;
				}
			}
			
			if($menu['active'] && isset($menu['title']) && !empty($menu['title'])){
				$this->breads[] = [
					'name'  => $this->getFirstUrl($menu),
					'title' => $menu['title'],
				];
			}
		}
		unset($menu);
		
		return $isActive;
	}
	
	/**
	 * 获取子菜单第一个url地址
	 *
	 * @param array $menu
	 * @return string
	 */
	protected function getFirstUrl($menu){
		if(isset($menu['name']) && strpos($menu['name'], '/')){
			return $menu['name'];
		}
		
		if(isset($menu['child']) && isset($menu['child'][0])){
			return $this->getFirstUrl($menu['child'][0]);
		}
		
		return '';
	}
	
	/**
	 * 检查请求参数
	 *
	 * @param string $queryStr
	 * @return bool
	 */
	protected function checkQueryParams($queryStr){
		parse_str($queryStr, $query);
		
		foreach($query as $k => $v){
			if(!isset($this->query[$k]) || $this->query[$k] != $v){
				return false;
			}
		}
		
		return true;
	}
	
}
