<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Wechat;

use Psr\SimpleCache\CacheInterface;
use Xin\Capsule\WithCache;
use Xin\Contracts\Wechat\Factory as WechatFactory;
use Xin\Support\File;

class WechatMediaManager
{

	use WithCache;

	/**
	 * @var \Xin\Contracts\Wechat\Factory
	 */
	protected $factory;

	/**
	 * @var string
	 */
	protected $prefix = 'wechat:media:';

	/**
	 * @param \Xin\Contracts\Wechat\Factory $factory
	 * @param CacheInterface|null $cache
	 */
	public function __construct(WechatFactory $factory, CacheInterface $cache = null)
	{
		$this->factory = $factory;
		$this->cache = $cache;
	}

	/**
	 * 获取小程序素材
	 *
	 * @param string $type
	 * @param string $fileUrl
	 * @return string
	 */
	public function miniProgram($type, $fileUrl)
	{
		$config = $this->factory->getConfig('mini_program');

		return $this->getMediaInfo($config['app_id'], $type, $fileUrl, function () use ($type, $fileUrl) {
			$localPath = File::putTempFile(file_get_contents($fileUrl));
			$result = $this->factory->miniProgram()->media->request('media/upload', 'POST', [
				'multipart' => [
					[
						'name' => 'type',
						'contents' => 'image',
					],
					[
						'name' => 'media',
						'contents' => fopen($localPath, 'r'),
						'filename' => basename($fileUrl),
					],
				],
				'connect_timeout' => 30,
				'timeout' => 30,
				'read_timeout' => 30,
			]);

			return isset($result['media_id']) ? $result['media_id'] : null;
		});
	}

	/**
	 * 获取小程序图片素材
	 *
	 * @param string $imgUrl
	 * @return string
	 */
	public function miniProgramImage($imgUrl)
	{
		return $this->miniProgram('image', $imgUrl);
	}

	/**
	 * 获取素材信息
	 *
	 * @param string $appId
	 * @param string $type
	 * @param string $fileUrl
	 * @param callable $make
	 * @return string
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	protected function getMediaInfo($appId, $type, $fileUrl, callable $make)
	{
		$cacheKey = $this->getCacheKey("{$appId}:{$type}:" . md5($fileUrl));

		$mediaIdInfo = $this->cache()->get($cacheKey);
		$mediaIdInfo = $mediaIdInfo ?: [
			'media_id' => '',
			'expire' => 0,
			'status' => 0,
		];

		$mediaId = $mediaIdInfo['media_id'];
		if ($mediaId && ($mediaIdInfo['expire'] > now()->addHour()->getTimestamp() || $mediaIdInfo['status'] == 1)) {
			return $mediaId;
		}

		$cacheTime = now()->addDays(3);

		$mediaIdInfo['status'] = 1;
		$this->cache()->set($cacheKey, $mediaIdInfo, $cacheTime);

		try {
			$mediaId = call_user_func($make);
		} catch (\Throwable $e) {
		}

		$mediaIdInfo['status'] = 0;
		$mediaIdInfo['media_id'] = $mediaId ?: '';
		$mediaIdInfo['expire'] = $cacheTime->copy()->subHours()->getTimestamp();

		$this->cache()->set($cacheKey, $mediaIdInfo, $cacheTime);

		return $mediaId;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function getCacheKey($name)
	{
		return $this->prefix . $name;
	}

	/**
	 * 创建实例
	 *
	 * @param \Xin\Contracts\Wechat\Factory $factory
	 * @param CacheInterface|null $cache
	 * @return static
	 */
	public static function of(WechatFactory $factory, CacheInterface $cache = null)
	{
		return new static($factory, $cache);
	}

}
