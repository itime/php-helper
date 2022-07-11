<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Xin\Thinkphp\View;

use Psr\SimpleCache\CacheInterface;
use think\App;
use Xin\Thinkphp\View\Storage\File as ViewStorageFile;

class Template
{

	use Concerns\ExtendThink,
		Concerns\FindTemplate,
		Concerns\TemplateParser;

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * 模板变量
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var array
	 */
	protected static $stacks = [];

	/**
	 * 模板配置参数
	 *
	 * @var array
	 */
	protected $config = [
		'auto_rule' => 1, // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
		'view_dir_name' => 'view', // 视图目录名
		'locations' => [], // 扩展视图目录

		'view_path' => '', // 模板路径
		'view_suffix' => 'html', // 默认模板文件后缀
		'view_depr' => DIRECTORY_SEPARATOR,
		'cache_path' => '',
		'cache_suffix' => 'php', // 默认模板缓存后缀
		'tpl_deny_func_list' => 'echo,exit', // 模板引擎禁用函数
		'tpl_deny_php' => false, // 默认模板引擎是否禁用PHP原生代码
		'tpl_begin' => '{', // 模板引擎普通标签开始标记
		'tpl_end' => '}', // 模板引擎普通标签结束标记
		'strip_space' => false, // 是否去除模板文件里面的html空格与换行
		'tpl_cache' => true, // 是否开启模板编译缓存,设为false则每次都会重新编译
		'compile_type' => 'file', // 模板编译类型
		'cache_prefix' => '', // 模板缓存前缀标识，可以动态改变
		'cache_time' => 0, // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
		'layout_on' => false, // 布局模板开关
		'layout_name' => 'layout', // 布局模板入口文件
		'layout_item' => '{__CONTENT__}', // 布局模板的内容替换标识
		'taglib_begin' => '{', // 标签库标签开始标记
		'taglib_end' => '}', // 标签库标签结束标记
		'taglib_load' => true, // 是否使用内置标签库之外的其它标签库，默认自动检测
		'taglib_build_in' => 'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
		'taglib_pre_load' => '', // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
		'display_cache' => false, // 模板渲染缓存
		'cache_id' => '', // 模板缓存ID
		'tpl_replace_string' => [],
		'tpl_var_identify' => 'array', // .语法变量识别，array|object|'', 为空时自动识别
		'default_filter' => 'htmlentities', // 默认过滤方法 用于普通标签输出
	];

	/**
	 * 扩展指令解析规则
	 *
	 * @var array
	 */
	protected $directive = [];

	/**
	 * 扩展解析规则
	 *
	 * @var array
	 */
	protected $extend = [];

	/**
	 * 模板包含信息
	 *
	 * @var array
	 */
	protected $includeFile = [];

	/**
	 * 模板存储对象
	 *
	 * @var ViewStorageFile
	 */
	protected $storage;

	/**
	 * 查询缓存对象
	 *
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 * 架构函数
	 *
	 * @param \think\App $app
	 * @param array $config
	 */
	public function __construct(App $app, $config = [])
	{
		$this->app = $app;

		$this->config = array_merge($this->config, (array)$config);

		if (empty($this->config['cache_path'])) {
			$this->config['cache_path'] = $app->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR;
		}

		$this->config['taglib_begin_origin'] = $this->config['taglib_begin'];
		$this->config['taglib_end_origin'] = $this->config['taglib_end'];

		$this->config['taglib_begin'] = preg_quote($this->config['taglib_begin'], '/');
		$this->config['taglib_end'] = preg_quote($this->config['taglib_end'], '/');
		$this->config['tpl_begin'] = preg_quote($this->config['tpl_begin'], '/');
		$this->config['tpl_end'] = preg_quote($this->config['tpl_end'], '/');

		// 初始化模板编译存储器
		$type = $this->config['compile_type'] ?: 'File';
		$class = false !== strpos($type, '\\') ? $type : '\\Xin\\Thinkphp\\View\\Storage\\' . ucwords($type);
		$this->storage = new $class();

		$this->cache = $app->cache;

		// 初始化扩展变量
		$this->extendThink();
		$this->extendRequest();

		// 扩展常用指令
		$this->directive('json', function ($vars) {
			return '<?php echo json_encode(' . $vars . ')?>';
		});
	}

	/**
	 * 渲染模板文件
	 *
	 * @access public
	 * @param string $template 模板文件
	 * @param array $vars 模板变量
	 * @return void
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function fetch(string $template, array $vars = []): void
	{
		if (empty($this->config['view_path'])) {
			$view = $this->config['view_dir_name'];

			if (is_dir($this->app->getAppPath() . $view)) {
				$path = $this->app->getAppPath() . $view . DIRECTORY_SEPARATOR;
			} else {
				$appName = $this->app->http->getName();
				$path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . ($appName ? $appName . DIRECTORY_SEPARATOR : '');
			}

			$this->config['view_path'] = $path;
		}

		if ($vars) {
			$this->data = array_merge($this->data, $vars);
		}

		if (!empty($this->config['cache_id']) && $this->config['display_cache'] && $this->cache) {
			// 读取渲染缓存
			if ($this->cache->has($this->config['cache_id'])) {
				echo $this->cache->get($this->config['cache_id']);

				return;
			}
		}

		if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
			// 获取模板文件名
			$template = $this->parseTemplate($template);
		}

		$cacheFile = $this->config['cache_path'] . $this->config['cache_prefix'] . md5($this->config['layout_on'] . $this->config['layout_name'] . $template) . '.' . ltrim($this->config['cache_suffix'], '.');

		if (!$this->checkCache($cacheFile)) {
			// 缓存无效 重新模板编译
			$content = file_get_contents($template);
			$this->compiler($content, $cacheFile);
		}

		// 页面缓存
		ob_start();
		if (PHP_VERSION > 8.0) {
			ob_implicit_flush(false);
		} else {
			ob_implicit_flush(0);
		}

		// 读取编译存储
		$this->storage->read($cacheFile, $this->data);

		// 获取并清空缓存
		$content = ob_get_clean();

		if (!empty($this->config['cache_id']) && $this->config['display_cache'] && $this->cache) {
			// 缓存页面输出
			$this->cache->set($this->config['cache_id'], $content, $this->config['cache_time']);
		}

		echo $content;
	}

	/**
	 * 渲染模板内容
	 *
	 * @access public
	 * @param string $content 模板内容
	 * @param array $vars 模板变量
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public function display(string $content, array $vars = []): void
	{
		if ($vars) {
			$this->data = array_merge($this->data, $vars);
		}

		$cacheFile = $this->config['cache_path'] . $this->config['cache_prefix'] . md5($content) . '.' . ltrim($this->config['cache_suffix'], '.');

		if (!$this->checkCache($cacheFile)) {
			// 缓存无效 模板编译
			$this->compiler($content, $cacheFile);
		}

		// 读取编译存储
		$this->storage->read($cacheFile, $this->data);
	}

	/**
	 * 编译模板文件内容
	 *
	 * @access private
	 * @param string $content 模板内容
	 * @param string $cacheFile 缓存文件名
	 * @return void
	 * @throws \Exception
	 */
	protected function compiler(string &$content, string $cacheFile): void
	{
		// 判断是否启用布局
		if ($this->config['layout_on']) {
			if (false !== strpos($content, '{__NOLAYOUT__}')) {
				// 可以单独定义不使用布局
				$content = str_replace('{__NOLAYOUT__}', '', $content);
			} else {
				// 读取布局模板
				$layoutFile = $this->parseTemplateFile($this->config['layout_name']);

				if ($layoutFile) {
					// 替换布局的主体内容
					$content = str_replace($this->config['layout_item'], $content, file_get_contents($layoutFile));
				}
			}
		} else {
			$content = str_replace('{__NOLAYOUT__}', '', $content);
		}

		// 模板解析
		$this->parse($content);

		if ($this->config['strip_space']) {
			/* 去除html空格与换行 */
			$find = ['~>\s+<~', '~>(\s+\n|\r)~'];
			$replace = ['><', '>'];
			$content = preg_replace($find, $replace, $content);
		}

		// 优化生成的php代码
		$content = preg_replace('/\?>\s*<\?php\s(?!echo\b|\bend)/s', '', $content);

		// 模板过滤输出
		$replace = $this->config['tpl_replace_string'];
		$content = str_replace(array_keys($replace), array_values($replace), $content);

		// 添加安全代码及模板引用记录
		$content = '<?php /*' . serialize($this->includeFile) . '*/ ?>' . "\n" . $content;
		// 编译存储
		$this->storage->write($cacheFile, $content);

		$this->includeFile = [];
	}

	/**
	 * 模板变量赋值
	 *
	 * @access public
	 * @param array $vars 模板变量
	 * @return $this
	 */
	public function assign(array $vars = [])
	{
		$this->data = array_merge($this->data, $vars);

		return $this;
	}

	/**
	 * 扩展模板解析规则
	 *
	 * @access public
	 * @param string $rule 解析规则
	 * @param callable|null $callback 解析规则
	 * @return void
	 */
	public function extend(string $rule, callable $callback = null): void
	{
		$this->extend[$rule] = $callback;
	}

	/**
	 * 扩展模板指令解析规则
	 *
	 * @access public
	 * @param string $rule 解析规则
	 * @param callable|null $callback 解析规则
	 * @return void
	 */
	public function directive(string $rule, callable $callback = null): void
	{
		$this->directive[$rule] = $callback;
	}

	/**
	 * 设置布局
	 *
	 * @access public
	 * @param mixed $name 布局模板名称 false 则关闭布局
	 * @param string $replace 布局模板内容替换标识
	 * @return $this
	 */
	public function layout($name, string $replace = '')
	{
		if (false === $name) {
			// 关闭布局
			$this->config['layout_on'] = false;
		} else {
			// 开启布局
			$this->config['layout_on'] = true;

			// 名称必须为字符串
			if (is_string($name)) {
				$this->config['layout_name'] = $name;
			}

			if (!empty($replace)) {
				$this->config['layout_item'] = $replace;
			}
		}

		return $this;
	}

	/**
	 * 检查编译缓存是否有效
	 * 如果无效则需要重新编译
	 *
	 * @access private
	 * @param string $cacheFile 缓存文件名
	 * @return bool
	 */
	protected function checkCache(string $cacheFile): bool
	{
		if (!$this->config['tpl_cache'] || !is_file($cacheFile) || !$handle = @fopen($cacheFile, "r")) {
			return false;
		}

		// 读取第一行
		preg_match('/\/\*(.+?)\*\//', fgets($handle), $matches);

		if (!isset($matches[1])) {
			return false;
		}

		$includeFile = unserialize($matches[1]);

		if (!is_array($includeFile)) {
			return false;
		}

		// 检查模板文件是否有更新
		foreach ($includeFile as $path => $time) {
			if (is_file($path) && filemtime($path) > $time) {
				// 模板文件如果有更新则缓存需要更新
				return false;
			}
		}

		// 检查编译存储是否有效
		return $this->storage->check($cacheFile, $this->config['cache_time']);
	}

	/**
	 * 检查编译缓存是否存在
	 *
	 * @access public
	 * @param string $cacheId 缓存的id
	 * @return boolean
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function isCache(string $cacheId): bool
	{
		if ($cacheId && $this->cache && $this->config['display_cache']) {
			// 缓存页面输出
			return $this->cache->has($cacheId);
		}

		return false;
	}

	/**
	 * 设置缓存对象
	 *
	 * @access public
	 * @param CacheInterface $cache 缓存对象
	 * @return void
	 */
	public function setCache(CacheInterface $cache): void
	{
		$this->cache = $cache;
	}

	/**
	 * 模板引擎配置
	 *
	 * @access public
	 * @param array $config
	 * @return $this
	 */
	public function config(array $config)
	{
		$this->config = array_merge($this->config, $config);

		return $this;
	}

	/**
	 * 获取模板引擎配置项
	 *
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function getConfig(string $name)
	{
		return $this->config[$name] ?? null;
	}

	/**
	 * 模板变量获取
	 *
	 * @access public
	 * @param string $name 变量名
	 * @return mixed
	 */
	public function get(string $name = '')
	{
		if ('' == $name) {
			return $this->data;
		}

		$data = $this->data;

		foreach (explode('.', $name) as $key => $val) {
			if (isset($data[$val])) {
				$data = $data[$val];
			} else {
				$data = null;
				break;
			}
		}

		return $data;
	}

	/**
	 * 获取堆内容
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getStack($name)
	{
		return isset(self::$stacks[$name]) ? self::$stacks[$name] : '';
	}

	/**
	 * 添加一块堆内容，后续使用
	 *
	 * @param string $name
	 * @param string $content
	 */
	public static function pushStack($name, $content)
	{
		if (!isset(self::$stacks[$name])) {
			self::$stacks[$name] = '';
		}

		self::$stacks[$name] .= "\n" . $content;
	}

	/**
	 * 模板引擎参数赋值
	 *
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->config[$name] = $value;
	}

	public function __debugInfo()
	{
		$data = get_object_vars($this);
		unset($data['storage']);

		return $data;
	}

}
