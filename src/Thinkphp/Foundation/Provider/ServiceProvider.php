<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Provider;

use think\App;
use think\Container;

abstract class ServiceProvider{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Cache
	 */
	protected $cache;

	/**
	 * @var \think\Session
	 */
	protected $session;

	/**
	 * @var \Xin\Thinkphp\Http\RequestOptimize
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
	 * @var \think\Route
	 */
	protected $route;

	/**
	 * Service constructor.
	 *
	 * @param App $app
	 */
	public function __construct(App $app = null){
		$this->app = $app ?: Container::get('app');
		$this->cache = $this->app->cache;
		$this->session = $this->app->session;
		$this->request = $this->app->request;
		$this->config = $this->app->config;
		$this->view = $this->app->view;
		$this->route = $this->app->route;
	}

	/**
	 * 注册服务
	 */
	public function register(){ }

	/**
	 * 启动
	 */
	public function boot(){ }
}
