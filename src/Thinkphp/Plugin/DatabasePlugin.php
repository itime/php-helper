<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Think\Model;

/**
 * Class DatabasePlugin
 *
 * @property-read string name
 * @property-read int    install
 */
class DatabasePlugin extends Model{
	
	/**
	 * @var string
	 */
	protected $name = 'plugin';
}
