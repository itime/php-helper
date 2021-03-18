<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

use think\model\concern\SoftDelete;
use think\model\Pivot;

class DatabaseAppPlugin extends Pivot{
	
	use SoftDelete;
	
	/**
	 * @var int
	 */
	protected $defaultSoftDelete = 0;
	
	/**
	 * @var string
	 */
	protected $name = 'app_plugin';
	
	/**
	 * @var bool
	 */
	protected $autoWriteTimestamp = true;
}
