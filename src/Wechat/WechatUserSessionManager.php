<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Wechat;

use Carbon\Carbon;
use Psr\SimpleCache\CacheInterface;
use Xin\Capsule\WithCache;

class WechatUserSessionManager {

	use WithCache;

	/**
	 * @var array
	 */
	protected $memoryData = [];

	/**
	 * @var string
	 */
	protected $prefix = 'wechat:user_session:';

	/**
	 * @param CacheInterface|null $cache
	 */
	public function __construct(CacheInterface $cache = null) {
		$this->cache = $cache;
	}

	/**
	 * 获取
	 * @param string $openid
	 * @return string
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public function get($openid) {
		return $this->getMemory($openid, function () use ($openid) {
			$key = $this->getCacheKey($openid);

			return $this->cache()->get($key);
		});
	}

	/**
	 * 直接从内存中获取
	 * @param string   $openid
	 * @param callable $resolver
	 * @return string
	 */
	protected function getMemory($openid, $resolver = null) {
		if (isset($this->memoryData[$openid])) {
			$item = $this->memoryData[$openid];
			if ($item['ttl'] <= now()->getTimestamp()) {
				$item = $this->memoryData[$openid] = $resolver();
			}
		} else {
			$item = $this->memoryData[$openid] = $resolver();
		}

		return $item ? $item['value'] : null;
	}

	/**
	 * 设置
	 * @param string $openid
	 * @param string $sessionKey
	 * @param mixed  $ttl
	 * @return void
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function set($openid, $sessionKey, $ttl = null) {
		$ttl = $ttl ?: now()->addSeconds(7000);
		$ttl = is_int($ttl) ? Carbon::createFromTimestamp($ttl) : $ttl;

		$item = [
			'ttl' => $ttl->getTimestamp(),
			'value' => $sessionKey,
		];

		$this->memoryData[$openid] = $item;

		$key = $this->getCacheKey($openid);
		$this->cache()->set($key, $item);
	}

	/**
	 * @param string $openid
	 * @return void
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function forget($openid) {
		$key = $this->getCacheKey($openid);
		$this->cache()->delete($key);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function getCacheKey($name) {
		return $this->prefix . $name;
	}

}
