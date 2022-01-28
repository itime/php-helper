<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Throwable;
use Xin\Contracts\Plugin\PluginNotFoundException as PluginNotFoundExceptionContract;

class PluginNotFoundException extends \Exception implements PluginNotFoundExceptionContract
{

	/**
	 * PluginNotFoundException constructor.
	 *
	 * @param string $plugin
	 * @param \Throwable $previous
	 */
	public function __construct($plugin, Throwable $previous = null)
	{
		parent::__construct("plugin {$plugin} not found!", 404, $previous);
	}

}
