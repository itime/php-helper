<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\VerifyCode;

use think\Model;
use Xin\Capsule\Service;
use Xin\Contracts\VerifyCode\Store;

class ModelStoreProvider extends Service implements Store
{

	/**
	 * @inerhitDoc
	 */
	public function save($type, $identifier, $code, $seconds)
	{
		return $this->newModel()->save([
			'type' => $type,
			'identifier' => $identifier,
			'code' => $code,
			'expire_time' => now()->addSeconds($seconds)->getTimestamp()
		]);
	}

	/**
	 * @inerhitDoc
	 */
	public function get($type, $identifier)
	{
		$info = $this->newModel()
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
		$this->newModel()
			->where('type', $type)
			->where('identifier', $identifier)
			->update([
				'status' => 1
			]);
	}

	/**
	 * @return Model
	 */
	protected function newModel()
	{
		$modelClass = $this->getConfig('model');

		return new $modelClass;
	}
}