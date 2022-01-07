<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Exception;
use think\exception\HttpException;

class PluginControllerNotFoundHttpException extends HttpException {

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * PluginControllerNotFoundHttpException constructor.
	 *
	 * @param string          $plugin
	 * @param string          $controller
	 * @param \Exception|null $previous
	 * @param array           $headers
	 * @param int             $code
	 */
	public function __construct($plugin, $controller, Exception $previous = null, array $headers = [], $code = 0) {
		parent::__construct(404, 'controller not exists:' . $controller, $previous, $headers, $code);
		$this->plugin = $plugin;
		$this->controller = $controller;
	}

	/**
	 * @return string
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

}
