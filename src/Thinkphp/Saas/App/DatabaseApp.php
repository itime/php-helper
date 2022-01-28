<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\App;

use think\Model;
use Xin\Support\Str;

/**
 * @property-read string access_id
 * @property-read string access_key
 */
class DatabaseApp extends Model
{

	use HasPlugins;

	const TITLE = '应用';

	/**
	 * @var string[]
	 */
	protected $readonly = [
		'access_id',
	];

	/**
	 * @var string
	 */
	protected $name = 'app';

	/**
	 * 插入数据
	 *
	 * @param DatabaseApp $model
	 * @return void
	 */
	public static function onBeforeInsert(DatabaseApp $model)
	{
		$model['access_id'] = substr(md5(microtime() . uniqid()), 0, 22);
		$model['access_key'] = Str::random(32);
	}

	/**
	 * 重置 access_key
	 * @param bool $save
	 * @return void
	 */
	public function resetAccessKey($save = true)
	{
		$this->setAttr('access_key', Str::random(32));
		if ($save) {
			$this->save();
		}
	}

}
