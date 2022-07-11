<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use think\db\Query;
use think\facade\Db;
use Xin\Capsule\Service;
use Xin\Contracts\Uploader\UploadProvider as UploadProviderContract;
use Xin\Support\Arr;

class UploadProvider extends Service implements UploadProviderContract
{
	/**
	 * @inheritDoc
	 * @return array|mixed|Query|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveById($scene, $id)
	{
		return $this->query()->where('type', $scene)->where('id', $id)->find();
	}

	/**
	 * @inheritDoc
	 * @return array|mixed|Query|\think\Model|null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveByHash($scene, $hashType, $hash)
	{
		return $this->query()->where('type', $scene)->where($hashType, $hash)->find();
	}

	/**
	 * @inheritDoc
	 */
	public function save($scene, array $data)
	{
		$saveData = Arr::transformKeys(Arr::only($data, [
			"type", "size", "mime_type",
			"md5", "sha1", "hash",
			"mimetype", "url",
		]), [
			'url' => 'path',
			'hash' => 'etag',
		]);
		$saveData = array_merge($saveData, [
			'type' => $scene,
			'create_time' => now()->getTimestamp()
		]);

		$saveData['id'] = $this->query()->insertGetId($saveData);

		return $saveData;
	}

	/**
	 * @return Query
	 */
	protected function query()
	{
		if (isset($this->config['model'])) {
			$class = $this->config['model'];
			return (new $class)->db();
		}

		$name = isset($this->config['name']) ? $this->config['name'] : 'file';

		return Db::name($name);
	}
}