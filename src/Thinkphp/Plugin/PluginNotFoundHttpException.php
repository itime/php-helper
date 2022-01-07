<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Exception;
use think\exception\HttpException;

class PluginNotFoundHttpException extends HttpException {

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * PluginNotFoundHttpException constructor.
	 *
	 * @param string          $plugin
	 * @param \Exception|null $previous
	 * @param array           $headers
	 * @param int             $code
	 */
	public function __construct($plugin, Exception $previous = null, array $headers = [], $code = 0) {
		parent::__construct(404, "plugin {$plugin} not exist.", $previous, $headers, $code);
		$this->plugin = $plugin;
	}

	/**
	 * @return string
	 */
	public function getPlugin() {
		return $this->plugin;
	}

}
