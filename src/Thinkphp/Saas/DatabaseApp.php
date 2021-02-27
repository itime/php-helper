<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

use think\Model;
use Xin\Support\Str;

/**
 * @property-read string access_id
 * @property-read string access_key
 */
class DatabaseApp extends Model{
	
	const TITLE = '应用';
	
	/**
	 * @var string[]
	 */
	protected $readonly = [
		'access_id', 'access_key',
	];
	
	/**
	 * @var string
	 */
	protected $name = 'app';
	
	public static function onBeforeInsert(DatabaseApp $model){
		$model->access_id = substr(md5(microtime().uniqid()), 0, 22);
		$model->access_key = Str::random(32);
	}
}
