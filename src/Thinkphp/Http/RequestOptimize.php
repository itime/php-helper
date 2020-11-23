<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Http;

use Xin\Support\Str;
use Xin\Support\Time;

/**
 * Trait RequestOptimize
 */
trait RequestOptimize{
	
	/**
	 * 数据源
	 *
	 * @var array
	 */
	protected $data = [];
	
	/**
	 * 插件
	 *
	 * @var string
	 */
	protected $plugin;
	
	/**
	 * 获取 ID list
	 *
	 * @param string $field
	 * @return array
	 */
	public function ids($field = 'ids'):array{
		$ids = $this->only([$field]);
		return Str::explode($ids[$field]);
	}
	
	/**
	 * 获取分页参数
	 *
	 * @param bool $withQuery
	 * @return array
	 */
	public function paginate($withQuery = true){
		$param = [
			'page' => $this->page(),
		];
		
		if($withQuery){
			$param['query'] = $this->get();
		}
		
		return $param;
	}
	
	/**
	 * 获取页码
	 *
	 * @return int
	 */
	public function page(){
		if(!isset($this->data['page'])){
			$page = $this->param('page/d', 0);
			$this->data['page'] = $page < 1 ? 1 : $page;
		}
		return $this->data['page'];
	}
	
	/**
	 * 获取分页条数
	 *
	 * @param int $max
	 * @param int $default
	 * @return int
	 */
	public function limit(int $max = 100, int $default = 20){
		if(!isset($this->data['limit'])){
			$limit = $this->param('limit/d', 0);
			if($limit < 1){
				$this->data['limit'] = $default;
			}else{
				$this->data['limit'] = $limit > $max ? $max : $limit;
			}
		}
		return $this->data['limit'];
	}
	
	/**
	 * 获取记录偏移数
	 *
	 * @return int
	 */
	public function offset(){
		if(!isset($this->data['offset'])){
			$offset = $this->param('offset/d', 0);
			$this->data['offset'] = $offset < 1 ? 1 : $offset;
		}
		return $this->data['offset'];
	}
	
	/**
	 * 获取排序的字段
	 *
	 * @return string
	 */
	public function sort():string{
		// todo 重新优化
		return isset($_GET['sort']) ? trim($_GET['sort']) : '';
	}
	
	/**
	 * 获取范围时间
	 *
	 * @param string $field
	 * @param int    $maxRange
	 * @param string $delimiter
	 * @return array
	 */
	public function rangeTime(string $field = 'datetime', int $maxRange = 0, string $delimiter = ' - '):array{
		$rangeTime = $this->param($field, '');
		return Time::parseRange($rangeTime, $maxRange, $delimiter);
	}
	
	/**
	 * 获取筛选关键字
	 *
	 * @param string $field
	 * @return string
	 */
	public function keywords(string $field = 'keywords'):string{
		$key = 'keywords_'.$field;
		if(!isset($this->data[$key])){
			if($this->has($field, 'get')){
				$keywords = $this->get($field);
			}else{
				$keywords = $this->post($field, '');
			}
			$keywords = trim($keywords);
			$this->data[$key] = $keywords;
		}
		return $this->data[$key];
	}
	
	/**
	 *  获取关键字SQL
	 *
	 * @param string $field
	 * @return array
	 */
	public function keywordsSql(string $field = 'keywords'):array{
		return build_keyword_sql($this->keywords($field));
	}
	
	/**
	 * 获取当前的模块名
	 *
	 * @access public
	 * @return string
	 */
	public function plugin():string{
		return $this->plugin ?: '';
	}
	
	/**
	 * 设置当前的插件名
	 *
	 * @access public
	 * @param string $plugin 插件名
	 * @return $this
	 */
	public function setPlugin(string $plugin):self{
		$this->plugin = $plugin;
		return $this;
	}
	
	/**
	 * 当前应用名称
	 *
	 * @return string
	 */
	public function appName(){
		return \app()->http->getName();
	}
	
	/**
	 * 前进地址
	 *
	 * @param mixed $default
	 * @return string
	 */
	public function forwardUrl($default = ''){
		$referer = $this->param("http_referer", '');
		
		if(empty($referer)){
			$referer = $this->server('HTTP_REFERER');
		}
		
		return $referer ?: (string)$default;
	}
}
