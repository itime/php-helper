<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use RuntimeException;
use Xin\Contracts\Saas\Wechat\ConfigProvider as ConfigProviderContract;
use Xin\Contracts\Saas\Wechat\WechatType;

class ConfigProvider implements ConfigProviderContract {

	/**
	 * @var int[]
	 */
	protected static $appTypeMaps = [
		WechatType::MINI_PROGRAM => 0,
		WechatType::OFFICIAL_ACCOUNT => 1,
	];

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveById($id, $type) {
		if (in_array($type, [WechatType::WORK, WechatType::OPEN_WORK])) {
			return $this->getByWorkId($id);
		} else {
			return $this->getByWechatAccountId($id);
		}
	}

	/**
	 * @param int $id
	 * @return array|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function getByWechatAccountId($id) {
		$info = DatabaseAccount::where('id', $id)->find();

		if (empty($info)) {
			return null;
		}

		return $info->toArray();
	}

	/**
	 * @param int $id
	 * @return mixed
	 * @todo
	 */
	protected function getByWorkId($id) {
		throw new RuntimeException("not implements work config read.");
	}

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveByAppId($appId, $type, $name = null) {
		if (in_array($type, [WechatType::WORK, WechatType::OPEN_WORK])) {
			return $this->getByWorkAppId($appId);
		} else {
			return $this->getByWechatAccountAppId($appId, $type);
		}
	}

	/**
	 * @param int $appId
	 * @return array|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @todo 开放平台
	 */
	protected function getByWechatAccountAppId($appId, $type) {
		$info = DatabaseAccount::where([
			'app_id' => $appId,
			'app_type' => static::$appTypeMaps[$type],
		])->find();

		if (empty($info)) {
			return null;
		}

		return $info->toArray();
	}

	/**
	 * @param int $appId
	 * @return mixed
	 * @todo
	 */
	protected function getByWorkAppId($appId) {
		throw new RuntimeException("not implements work config read.");
	}

}
