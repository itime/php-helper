<?php

namespace Xin\Excel\Cache;

use Psr\SimpleCache\CacheInterface;
use think\facade\Cache;
use Xin\Support\Manager;

class CacheManager extends Manager
{

	/**
	 * @const string
	 */
	public const DRIVER_BATCH = 'batch';

	/**
	 * @const string
	 */
	public const DRIVER_MEMORY = 'memory';

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return config('excel.cache.driver', 'memory');
	}

	/**
	 * @return MemoryCache
	 */
	public function createMemoryDriver(): CacheInterface
	{
		return new MemoryCache(
			config('excel.cache.batch.memory_limit', 600000)
		);
	}

	/**
	 * @return BatchCache
	 */
	public function createBatchDriver(): CacheInterface
	{
		return new BatchCache(
			$this->createIlluminateDriver(),
			$this->createMemoryDriver()
		);
	}

	/**
	 * @return CacheInterface
	 */
	public function createIlluminateDriver(): CacheInterface
	{
		return Cache::store(
			config('excel.cache.store')
		);
	}

	public function flush()
	{
		$this->driver()->clear();
	}

	public function isInMemory(): bool
	{
		return $this->getDefaultDriver() === self::DRIVER_MEMORY;
	}

	protected function resolveType($name)
	{
		return $name;
	}

	protected function resolveConfig($name)
	{
		return [];
	}

}
