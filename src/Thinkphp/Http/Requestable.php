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
 * @mixin \think\Request
 */
trait Requestable{

	use HasValidate, HasPlatform, HasUser, HasApp;

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
	 * @var string
	 */
	protected $path = null;

	/**
	 * 获取 ID list
	 *
	 * @param string $field
	 * @return array
	 */
	public function ids($field = 'ids'):array{
		$ids = $this->only([$field]);
		return isset($ids[$field]) ? Str::explode($ids[$field]) : [];
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

		if($this->has('limit')){
			$param['list_rows'] = $this->limit();
		}

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
	public function page():int{
		if(!isset($this->data['page'])){
			$page = $this->param('page/d', 0);
			$this->data['page'] = $page < 1 ? 1 : $page;
		}

		return (int)$this->data['page'];
	}

	/**
	 * 获取分页条数
	 *
	 * @param int $max
	 * @param int $default
	 * @return int
	 */
	public function limit(int $max = 100, int $default = 15):int{
		if(!isset($this->data['limit'])){
			$limit = $this->param('limit/d', 0);
			if($limit < 1){
				$this->data['limit'] = $default;
			}else{
				$this->data['limit'] = $limit > $max ? $max : $limit;
			}
		}

		return (int)$this->data['limit'];
	}

	/**
	 * 获取记录偏移数
	 *
	 * @return int
	 */
	public function offset():int{
		if(!isset($this->data['offset'])){
			$offset = $this->param('offset/d', 0);
			$this->data['offset'] = $offset < 1 ? 1 : $offset;
		}

		return (int)$this->data['offset'];
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
	public function rangeTime(string $field = 'datetime', int $maxRange = 0, string $delimiter = ' ~ '):array{
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
				$keywords = $this->get($field.'/s', '');
			}else{
				$keywords = $this->post($field.'/s', '');
			}
			$keywords = trim($keywords);
			$keywords = Str::rejectEmoji($keywords);
			$this->data[$key] = $keywords ?? '';
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
	 * @param string $key
	 * @return mixed|null
	 */
	public function postJSON(string $key){
		if(!$this->has($key, 'post')){
			return null;
		}

		$value = $this->post($key);
		return json_decode($value, true, 512, JSON_PRESERVE_ZERO_FRACTION);
	}

	/**
	 * Generates the normalized query string for the Request.
	 * It builds a normalized query string, where keys/value pairs are alphabetized
	 * and have consistent escaping.
	 *
	 * @return string A normalized query string for the Request
	 */
	public function getQueryString(){
		return static::normalizeQueryString($this->query());
	}

	/**
	 * 包含自定义参数的全路径地址
	 *
	 * @param array $query
	 * @param bool  $complete
	 * @return string
	 */
	public function urlWithQuery(array $query, $complete = false){
		$queryString = $this->query();
		parse_str($queryString, $queryString);
		$query = array_merge($queryString, $query);

		return count($query) > 0
			? $this->baseUrl($complete)."?".static::normalizeQueryString($query)
			: $this->baseUrl($complete);
	}

	/**
	 * Determine if the request is the result of an prefetch call.
	 *
	 * @return bool
	 */
	public function prefetch(){
		return strcasecmp($this->server('HTTP_X_MOZ'), 'prefetch') === 0 ||
			strcasecmp($this->header('Purpose'), 'prefetch') === 0;
	}

	/**
	 * 从Cookie中获取前一个地址
	 *
	 * @param string $fallback
	 * @return string
	 */
	public function previousUrl($fallback = null){
		$url = $this->cookie('_previous_url');

		if(!$url){
			$url = (string)url($fallback);
		}

		return $url;
	}

	/**
	 * 获取当前请求路径（是否包含应用名称）
	 *
	 * @param bool $complete
	 * @return string
	 */
	public function path($complete = false){
		if(is_null($this->path) === false){
			return $this->path;
		}

		$pathinfo = $this->pathinfo();
		if($complete){
			$pathinfo = $this->root().'/'.$pathinfo;
		}
		$pathinfo = trim($pathinfo, '/');

		$suffix = app()->route->config('url_html_suffix');
		if(false === $suffix){
			// 禁止伪静态访问
			$path = $pathinfo;
		}elseif($suffix){
			// 去除正常的URL后缀
			$path = preg_replace('/\.('.ltrim($suffix, '.').')$/i', '', $pathinfo);
		}else{
			// 允许任何后缀访问
			$path = preg_replace('/\.'.$this->ext().'$/i', '', $pathinfo);
		}

		return $path;
	}

	/**
	 * 验证当前路由地址是否是给定的规则
	 *
	 * @param string|array $patterns
	 * @return bool
	 */
	public function pathIs($patterns){
		return Str::is($patterns, $this->path());
	}

	/**
	 * Normalizes a query string.
	 * It builds a normalized query string, where keys/value pairs are alphabetized,
	 * have consistent escaping and unneeded delimiters are removed.
	 *
	 * @param string $qs Query string
	 * @return string A normalized query string for the Request
	 */
	public static function normalizeQueryString($qs){
		if(empty($qs)){
			return '';
		}

		parse_str($qs, $qs);

		/** @var array $qs */
		ksort($qs);

		return http_build_query($qs, '', '&', \PHP_QUERY_RFC3986);
	}
}
