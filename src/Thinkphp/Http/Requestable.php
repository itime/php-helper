<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Http;

use Symfony\Component\HttpFoundation\AcceptHeader;
use Xin\Support\Arr;
use Xin\Support\Str;
use Xin\Support\Time;
use Xin\Thinkphp\Plugin\Url;

/**
 * @mixin \think\Request
 */
trait Requestable
{
	use HasValidate, HasPlatform,
		HasUser, HasApp, HasPlugin,
		HasContentTypes;

	/**
	 * @var string
	 */
	protected $currentPath;

	/**
	 * @var array
	 */
	protected $acceptableContentTypes;

	/**
	 * @var array
	 */
	protected $charsets;

	/**
	 * @var array
	 */
	protected $encodings;

	/**
	 * 获取 ID list
	 *
	 * @param string $field
	 * @return array
	 */
	public function ids($field = 'ids'): array
	{
		$ids = $this->only([$field]);

		return isset($ids[$field]) ? Str::explode($ids[$field]) : [];
	}

	/**
	 * 获取分页参数
	 *
	 * @param bool $withQuery
	 * @return array
	 */
	public function paginate($withQuery = true)
	{
		$param = [
			'page' => $this->page(),
		];

		if ($this->has('limit')) {
			$param['list_rows'] = $this->limit();
		}

		if ($withQuery) {
			$param['query'] = $this->get();
		}

		return $param;
	}

	/**
	 * 获取页码
	 *
	 * @return int
	 */
	public function page(): int
	{
		$page = $this->param('page/d', 0);
		$page = max($page, 1);

		return (int)$page;
	}

	/**
	 * 获取分页条数
	 *
	 * @param int $max
	 * @param int $default
	 * @return int
	 */
	public function limit(int $max = 100, int $default = 15): int
	{
		$limit = $this->param('limit/d', 0);
		if ($limit < 1) {
			$limit = $default;
		} else {
			$limit = min($limit, $max);
		}

		return (int)$limit;
	}

	/**
	 * 获取记录偏移数
	 *
	 * @return int
	 */
	public function offset(): int
	{
		$offset = $this->param('offset/d', 0);
		$offset = max($offset, 1);

		return (int)$offset;
	}

	/**
	 * 获取排序的字段
	 *
	 * @return string
	 */
	public function sort(): string
	{
		// todo 重新优化
		return $this->param('sort', '');
	}

	/**
	 * 获取范围时间
	 *
	 * @param string $field
	 * @param int $maxRange
	 * @param string $delimiter
	 * @return array
	 */
	public function rangeTime(string $field = 'datetime', int $maxRange = 0, string $delimiter = ' ~ '): array
	{
		$rangeTime = $this->param($field, '');

		return Time::parseRange($rangeTime, $maxRange, $delimiter);
	}

	/**
	 * 获取筛选关键字
	 *
	 * @param string $field
	 * @return string
	 */
	public function keywords(string $field = 'keywords'): string
	{
		if ($this->has($field, 'get')) {
			$keywords = $this->get($field . '/s', '');
		} else {
			$keywords = $this->post($field . '/s', '');
		}
		$keywords = trim($keywords);

		return Str::rejectEmoji($keywords);
	}

	/**
	 *  获取关键字SQL
	 *
	 * @param string $field
	 * @return array
	 */
	public function keywordsSql(string $field = 'keywords'): array
	{
		return keywords_build_sql($this->keywords($field));
	}

	/**
	 * 读取POST请求JSON字段
	 * @param string $key
	 * @return array|null
	 */
	public function postJSON(string $key)
	{
		return $this->json($key, 'post');
	}

	/**
	 * 读取PUT请求JSON字段
	 * @param string $key
	 * @return array|null
	 */
	public function putJSON(string $key)
	{
		return $this->json($key, 'post');
	}

	/**
	 * 读取json数据
	 * @param string $key
	 * @param string $method
	 * @return array|null
	 */
	public function json($key, $method = 'param')
	{
		if (!$this->has($key, $method)) {
			return null;
		}

		$value = $this->$method($key);

		return json_decode($value, true, 512, JSON_PRESERVE_ZERO_FRACTION);
	}

	/**
	 * 智能读取数据
	 * @param array $excludes
	 * @param string $filter
	 * @return array
	 */
	public function data($excludes = [], $filter = '')
	{
		$method = $this->method(true);

		// 自动获取请求变量
		switch ($method) {
			case 'POST':
				$data = $this->post('', null, $filter);
				break;
			case 'PUT':
			case 'DELETE':
			case 'PATCH':
				$data = $this->put('', null, $filter);
				break;
			default:
				$data = $this->get('', null, $filter);
				break;
		}

		return Arr::except($data, $excludes);
	}

	/**
	 * 智能读取数据
	 * @param array $onlyKeys
	 * @param string $filter
	 * @return array
	 */
	public function dataOnly($onlyKeys = [], $filter = '')
	{
		$method = $this->method(true);

		// 自动获取请求变量
		switch ($method) {
			case 'POST':
				$type = "post";
				break;
			case 'PUT':
			case 'DELETE':
			case 'PATCH':
				$type = "put";
				break;
			default:
				$type = "get";
				break;
		}

		return $this->only($onlyKeys, $type, $filter);
	}

	/**
	 * Generates the normalized query string for the Request.
	 * It builds a normalized query string, where keys/value pairs are alphabetized
	 * and have consistent escaping.
	 *
	 * @return string A normalized query string for the Request
	 */
	public function getQueryString()
	{
		return static::normalizeQueryString($this->query());
	}

	/**
	 * 包含自定义参数的全路径地址
	 *
	 * @param array $query
	 * @param bool $complete
	 * @return string
	 */
	public function urlWithQuery(array $query, $complete = false)
	{
		$queryString = $this->query();
		parse_str($queryString, $originalQuery);
		$query = array_merge($originalQuery, $query);

		return count($query) > 0
			? $this->baseUrl($complete) . "?" . static::normalizeQueryString($query)
			: $this->baseUrl($complete);
	}

	/**
	 * Determine if the request is the result of an prefetch call.
	 *
	 * @return bool
	 */
	public function prefetch()
	{
		return strcasecmp($this->server('HTTP_X_MOZ'), 'prefetch') === 0 ||
			strcasecmp($this->header('Purpose'), 'prefetch') === 0;
	}

	/**
	 * 从Cookie中获取前一个地址
	 *
	 * @param string $fallback
	 * @return string
	 */
	public function previousUrl($fallback = null)
	{
		$url = $this->cookie('_previous_url');

		if (!$url) {
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
	public function path($complete = false)
	{
		if ($this->currentPath === null) {
			$pathinfo = $this->pathinfo();
			$pathinfo = trim($pathinfo, '/');

			$suffix = app()->route->config('url_html_suffix');
			if (false === $suffix) {
				// 禁止伪静态访问
				$path = $pathinfo;
			} elseif ($suffix) {
				// 去除正常的URL后缀
				$path = preg_replace('/\.(' . ltrim($suffix, '.') . ')$/i', '', $pathinfo);
			} else {
				// 允许任何后缀访问
				$path = preg_replace('/\.' . $this->ext() . '$/i', '', $pathinfo);
			}

			$this->currentPath = $path;
		}

		return $complete ? $this->root() . '/' . $this->currentPath : $this->currentPath;
	}

	/**
	 * 获取当前请求路径并解析插件路径
	 * @return string
	 */
	public function pathWithParsePlugin()
	{
		$path = $this->path(false);

		if (Url::$pluginPrefix && strpos($path, Url::$pluginPrefix . "/") === 0) {
			$info = explode('/', $path, 3);
			if (isset($info[1])) {
				$plugin = $info[1];
				$path = isset($info[2]) ? $info[2] : '';

				$path = $plugin . ">" . $path;
			}
		}

		return $path;
	}

	/**
	 * 验证当前路由地址是否是给定的规则
	 *
	 * @param string|array $patterns
	 * @return bool
	 */
	public function pathIs($patterns)
	{
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
	public static function normalizeQueryString($qs)
	{
		if (empty($qs)) {
			return '';
		}

		parse_str($qs, $qs);

		/** @var array $qs */
		ksort($qs);

		return http_build_query($qs, '', '&', \PHP_QUERY_RFC3986);
	}

	/**
	 * Gets a list of content types acceptable by the client browser in preferable order.
	 *
	 * @return array
	 */
	public function getAcceptableContentTypes()
	{
		if (null !== $this->acceptableContentTypes) {
			return $this->acceptableContentTypes;
		}

		return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->header('Accept'))->all());
	}

	/**
	 * Gets a list of charsets acceptable by the client browser in preferable order.
	 *
	 * @return array
	 */
	public function getCharsets()
	{
		if (null !== $this->charsets) {
			return $this->charsets;
		}

		return $this->charsets = array_keys(AcceptHeader::fromString($this->header('Accept-Charset'))->all());
	}

	/**
	 * Gets a list of encodings acceptable by the client browser in preferable order.
	 *
	 * @return array
	 */
	public function getEncodings()
	{
		if (null !== $this->encodings) {
			return $this->encodings;
		}

		return $this->encodings = array_keys(AcceptHeader::fromString($this->header('Accept-Encoding'))->all());
	}
}
