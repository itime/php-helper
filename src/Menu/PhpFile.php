<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

class PhpFile extends Driver{
	
	/**
	 * @var array
	 */
	protected $data;
	
	/**
	 * 初始化加载文件
	 */
	protected function load(){
		if(!is_null($this->data)){
			return;
		}
		
		$targetPath = $this->config('target_path');
		if(empty($targetPath) || !file_exists($targetPath)){
			$this->data = require_once $this->config('base_path');
		}else{
			$this->data = require_once $targetPath;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function get($user){
		$this->load();
		return $this->data;
	}
	
	/**
	 * @inheritDoc
	 */
	public function puts($menus, $append = []){
		$this->load();
		
		foreach($menus as $menu){
			$this->insert($menu, $append);
		}
		
		$this->write();
	}
	
	/**
	 * 插入一个菜单
	 *
	 * @param array $menu
	 * @param array $append
	 */
	protected function insert($menu, $append = []){
		$menu = array_merge($menu, $append);
		
		if(isset($menu['child'])){
			self::each(function(&$item) use ($append){
				$item = array_merge($item, $append);
			}, $menu['child']);
		}
		
		if(isset($menu['parent'])){
			foreach($this->data as &$item){
				if($item['name'] == $menu['parent']){
					if(isset($item['child'])){
						$item['child'] = [];
					}
					
					$item['child'][] = $menu;
					break;
				}
			}
			unset($item);
		}else{
			$this->data[] = $menu;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function forget($name){
		$this->load();
		
		if(is_numeric($name)){
			unset($this->data[$name]);
		}elseif(is_callable($name)){
			$this->eachDelete($name, $this->data);
		}elseif(is_string($name)){
			$this->eachDelete(function($item) use ($name){
				return $name == $item['name'];
			}, $this->data);
		}else{
			$this->eachDelete(function($item) use ($name){
				return empty(array_diff_assoc($item, $name));
			}, $this->data);
		}
		
		$this->write();
	}
	
	/**
	 * 遍历删除菜单
	 *
	 * @param callable $callback
	 * @param array    $menus
	 */
	protected static function eachDelete($callback, &$menus){
		foreach($menus as $key => &$menu){
			if(call_user_func_array($callback, [$menu]) === true){
				unset($menus[$key]);
			}elseif(isset($menu['child'])){
				self::eachDelete($callback, $menu['child']);
			}
		}
		unset($menu);
	}
	
	/**
	 * 写入数据
	 */
	protected function write($ignoreError = false){
		$targetPath = $this->config('target_path');
		if(empty($targetPath)){
			if(!$ignoreError){
				throw new \RuntimeException("target_path not configutre.");
			}
			
			return;
		}
		
		$content = "<?php\nreturn ".var_export($this->data, true).";";
		file_put_contents($targetPath, $content);
	}
	
}
