<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Weight;

use think\Config;
use think\View;
use Xin\Thinkphp\Http\RequestOptimize;

/**
 * Class Weight
 *
 * @property-read \think\Request $request
 * @property-read \think\Config  $config
 * @property-read \think\View    $view
 */
class Weight{

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
	 * @param \Xin\Thinkphp\Http\RequestOptimize $request
	 * @param \think\Config                      $config
	 */
	public function __construct(RequestOptimize $request, Config $config){
		$this->request = $request;
		$this->config = $config;

		$this->view = new View();
		$this->view->init($this->config->pull('template'));
	}

}
