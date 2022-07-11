<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Capsule;

use Psr\SimpleCache\CacheInterface;

trait WithCache
{

	/**
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 * @var callable
	 */
	protected static $defaultCacheResolver;

	/**
	 * 获取缓存器
	 * @return CacheInterface
	 */
	public function cache()
	{
		if (!$this->cache) {
			$this->cache = static::getDefaultCache();
		}

		return $this->cache;
	}

	/**
	 * 设置缓存器
	 * @param CacheInterface $cache
	 */
	public function setCache(CacheInterface $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * 获取默认缓存
	 * @return CacheInterface
	 */
	public static function getDefaultCache()
	{
		if (is_callable(static::$defaultCacheResolver)) {
			return call_user_func(static::$defaultCacheResolver);
		}

		throw new \RuntimeException("cache resolver not implements!");
	}

	/**
	 * 设置缓存默认缓存解析器
	 * @param callable $cacheResolver
	 */
	public static function setDefaultCacheResolver(callable $cacheResolver)
	{
		static::$defaultCacheResolver = $cacheResolver;
	}

}
