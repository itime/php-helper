<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Contracts\Menu\Generator as GeneratorContract;

class Generator implements GeneratorContract{
	
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
	 * @var bool
	 */
	protected $isAdministrator = false;
	
	/**
	 * @var bool
	 */
	protected $isDevelop = false;
	
	/**
	 * @inheritDoc
	 */
	public function generate(array $menus, array $options = []){
		$this->menus = $menus;
		$this->query = isset($options['query']) ? $options['query'] : [];
		$this->isAdministrator = isset($options['is_administrator']) ? $options['is_administrator'] : false;
		$this->isDevelop = isset($options['is_develop']) ? $options['is_develop'] : false;
		
		$this->tree($options['rule'], $this->menus);
		
		$this->breads = array_reverse($this->breads);
		array_pop($this->breads);
		
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
			$menu['show'] = $this->resolveIshow($menu);
			
			if(isset($menu['child']) && $this->tree($rule, $menu['child'])){
				$menu['active'] = $isActive = true;
			}elseif($menu['url'] != ''){
				$menuRule = explode("?", $menu['url'], 2);
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
	 * 解析菜单是否显示
	 *
	 * @param array $menu
	 * @return bool
	 */
	protected function resolveIshow(array $menu){
		if(isset($menu['only_admin']) && $menu['only_admin']){
			if(!$this->isAdministrator){
				return false;
			}
		}
		
		if(isset($menu['only_dev']) && $menu['only_dev']){
			if(!$this->isDevelop){
				return false;
			}
		}
		
		return isset($menu['show']) ? (bool)value($menu['show']) : false;
	}
	
	/**
	 * 获取子菜单第一个url地址
	 *
	 * @param array $menu
	 * @return string
	 */
	protected function getFirstUrl($menu){
		if(isset($menu['url']) && strpos($menu['url'], '/')){
			return $menu['url'];
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
