<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Support\Arr;

class PhpFile extends Driver{

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @var callable
	 */
	protected $loadCallback;

	/**
	 * 初始化加载文件
	 */
	protected function load(){
		if(!is_null($this->data)){
			return;
		}

		$targetPath = $this->config('target_path');
		if(!file_exists($targetPath) && $this->loadCallback){
			call_user_func($this->loadCallback);
		}

		if(empty($targetPath) || !file_exists($targetPath)){
			$basePath = $this->config('base_path');
			if(!file_exists($basePath)){
				throw new \RuntimeException("菜单初始文件不存在[{$basePath}]");
			}
			$this->data = require_once $basePath;
		}else{
			$this->data = require_once $targetPath;
		}
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
	public function get($filter = null){
		$this->load();
		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function puts($menus, $plugin = null, $append = []){
		$plugin = empty($plugin) ? '' : $plugin;

		$this->load();

		foreach($menus as $menu){
			$this->insert($menu, $plugin, $append);
		}

		$this->write();
	}

	/**
	 * 插入一个菜单
	 *
	 * @param array $menu
	 * @param array $append
	 */
	protected function insert($menu, $plugin, $append = []){
		$menu = array_merge($menu, $append);
		$menu['plugin'] = $plugin;
		$menu['link'] = $menu['link'] ?? (isset($menu['child']) ? 0 : 1) ?? 1;

		if(isset($menu['child'])){
			self::eachTree(function(&$item) use ($plugin, $append){
				$item = array_merge($item, $append);
				$item['plugin'] = $plugin;
			}, $menu['child']);
		}

		if(isset($menu['parent'])){
			foreach($this->data as &$item){
				if($item['url'] == $menu['parent']){
					if(!isset($item['child'])){
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
	public function forget($condition){
		$this->load();

		if(is_numeric($condition)){
			unset($this->data[$condition]);
		}elseif(is_callable($condition)){
			static::eachTreeFilter($condition, $this->data);
		}elseif(is_string($condition)){
			static::eachTreeFilter(function($item) use ($condition){
				return $condition == $item['url'];
			}, $this->data);
		}else{
			static::eachTreeFilter(function($item) use ($condition){
				return Arr::where($item, $condition);
				// return empty(array_diff_assoc($item, $condition));
			}, $this->data);
		}

		$this->write();
	}

	/**
	 * 写入数据
	 *
	 * @param bool $ignoreError
	 */
	protected function write($ignoreError = false){
		$targetPath = $this->config('target_path');
		if(empty($targetPath)){
			if(!$ignoreError){
				throw new \RuntimeException("target_path not configutre.");
			}

			return;
		}

		// 数组排序
		self::eachTree(function(&$item, &$parent = null){
			if(isset($item['child'])){
				$this->sort($item['child']);
			}
		}, $this->data);
		$this->sort($this->data);

		$content = "<?php\nreturn ".var_export($this->data, true).";";
		file_put_contents($targetPath, $content);
	}

	/**
	 * 数组排序
	 *
	 * @param array $list
	 */
	protected function sort(array &$list){
		usort($list, function($it1, $it2){
			$sort1 = isset($it1['sort']) ? $it1['sort'] : 0;
			$sort2 = isset($it2['sort']) ? $it2['sort'] : 0;

			return $sort1 == $sort2 ? 0 : ($sort1 > $sort2 ? 1 : -1);
		});
	}

	/**
	 * 刷新菜单
	 *
	 * @param string $plugin
	 * @return bool
	 */
	public function refresh($plugin = null){
		if(empty($plugin)){
			$this->data = null;
			$targetPath = $this->config('target_path');
			if(file_exists($targetPath)){
				unlink($targetPath);
			}
		}else{
			$this->forget(function($item) use ($plugin){
				return isset($item['plugin']) && $item['plugin'] == $plugin;
			});
		}
		return true;
	}

	/**
	 * @param callable $loadCallback
	 */
	public function setLoadCallback(callable $loadCallback):void{
		$this->loadCallback = $loadCallback;
	}

}
