<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use think\App;
use think\View;

/**
 * @property-read \think\App $app
 * @property-read \think\Request $request
 * @property-read \think\Config $config
 * @property-read \think\View $view
 */
abstract class Weight
{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * @var \think\Config
	 */
	protected $config;

	/**
	 * @var \think\View
	 */
	protected $view;

	/**
	 * Weight constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->request = $app['request'];
		$this->config = $app['config'];

		$this->view = new View($app);

		$classPath = str_replace("\\", "/", get_class($this));
		$this->view->engine()->config([
			"view_dir_name" => "view",
			"view_path" => root_path(dirname($classPath)),
		]);
	}

	/**
	 * 中控入口
	 *
	 * @param mixed ...$args
	 */
	public function handle(...$args)
	{
		echo call_user_func_array([$this, 'render'], $args);
	}

	//	/**
	//	 * 渲染
	//	 *
	//	 * @return string
	//	 */
	//	abstract protected function render();

	/**
	 * 渲染模板
	 *
	 * @param string $template
	 * @param array $vars
	 * @return string
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	protected function fetch($template = '', $vars = [])
	{
		return $this->view->fetch($template, $vars);
	}

	/**
	 * 渲染内容
	 *
	 * @param string $content
	 * @param array $vars
	 * @return string
	 */
	protected function display($content, $vars = [])
	{
		return $this->view->display($content, $vars);
	}

	/**
	 * 模板变量赋值
	 *
	 * @access public
	 * @param string|array $name 模板变量
	 * @param mixed $value 变量值
	 * @return $this
	 */
	protected function assign($name, $value = null)
	{
		$this->view->assign($name, $value);

		return $this;
	}

}
