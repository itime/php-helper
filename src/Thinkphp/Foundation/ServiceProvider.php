<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation;

use think\App;
use think\Container;
use Xin\Thinkphp\Http\Requestable;

/**
 * @property-read \think\Request|Requestable request
 * @property-read \think\Config              config
 * @property-read \think\Session             session
 * @property-read \think\View                view
 * @property-read \think\Cache               cache
 * @property-read \think\Route               route
 */
abstract class ServiceProvider{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * Service constructor.
	 *
	 * @param \think\App|null $app
	 */
	public function __construct(App $app = null){
		$this->app = $app ?: Container::get('app');
	}

	/**
	 * 注册服务
	 */
	public function register(){ }

	/**
	 * 启动
	 */
	public function boot(){ }

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->app[$name];
	}
}
