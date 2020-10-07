<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\App;
use Xin\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * PluginManager constructor.
	 *
	 * @param \think\App $app
	 * @param array      $config
	 */
	public function __construct(App $app, array $config){
		parent::__construct($config);
		
		$this->app = $app;
	}
	
}
