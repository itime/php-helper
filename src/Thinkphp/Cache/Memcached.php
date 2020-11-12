<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Cache;

use think\cache\driver\Memcached as MemcachedDriver;
use Xin\Cache\MemcachedLock;
use Xin\Contracts\Cache\LockProvider;

class Memcached extends MemcachedDriver implements LockProvider{
	
	/**
	 * @inheritDoc
	 * @noinspection PhpParamsInspection
	 */
	public function lock($name, $seconds = 0, $owner = null){
		return new MemcachedLock($this->handler, $this->getCacheKey($name), $seconds, $owner);
	}
	
	/**
	 * @inheritDoc
	 */
	public function restoreLock($name, $owner){
		return $this->lock($name, 0, $owner);
	}
}
