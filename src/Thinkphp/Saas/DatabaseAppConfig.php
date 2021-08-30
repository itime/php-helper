<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Saas;

use think\Model;

/**
 * @property-read int    app_id
 * @property-read string name
 * @property array       config
 */
class DatabaseAppConfig extends Model{

	/**
	 * @var string[]
	 */
	protected $readonly = [
		'name',
	];

	/**
	 * @var string
	 */
	protected $name = 'app_config';

	/**
	 * @var bool
	 */
	protected $autoWriteTimestamp = true;

	/**
	 * @var array
	 */
	protected $type = [
		'config' => 'array',
	];
}
