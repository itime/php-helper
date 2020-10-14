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
 * @property-read \think\App     $app
 * @property-read \think\Request $request
 * @property-read \think\Config  $config
 * @property-read \think\View    $view
 */
class Weight{
	
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
	public function __construct(App $app){
		$this->app = $app;
		$this->request = $app['request'];
		$this->config = $app['config'];
		
		$this->view = new View($app);
		
		$classPath = str_replace("\\", "/", get_class($this));
		$this->view->engine()->config([
			"view_dir_name" => "view",
			"view_path"     => root_path(dirname($classPath)),
		]);
	}
	
}
