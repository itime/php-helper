<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\VerifyCode;

use think\facade\Db;
use Xin\Capsule\Service;
use Xin\Contracts\VerifyCode\Store;

class DatabaseStoreProvider extends Service implements Store
{

	/**
	 * @inerhitDoc
	 */
	public function save($type, $identifier, $code, $seconds)
	{
		return $this->newDb()->insert([
			'type' => $type,
			'identifier' => $identifier,
			'code' => $code,
			'expire_time' => now()->addSeconds($seconds)->getTimestamp(),
			'create_time' => now()->getTimestamp()
		]);
	}

	/**
	 * @inerhitDoc
	 */
	public function get($type, $identifier)
	{
		$info = $this->newDb()
			->where('type', $type)
			->where('identifier', $identifier)
			->where('expire_time', '>', now()->getTimestamp())
			->order('id desc')
			->find();

		if ($info && $info['status'] == 0) {
			return $info['code'];
		}

		return null;
	}

	/**
	 * @inerhitDoc
	 */
	public function forget($type, $identifier)
	{
		$this->newDb()
			->where('type', $type)
			->where('identifier', $identifier)
			->update([
				'status' => 1
			]);
	}

	/**
	 * @return Db
	 */
	protected function newDb()
	{
		$table = $this->getConfig('table');

		return Db::name($table);
	}
}