<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Cache;

use think\cache\driver\Redis as RedisDriver;
use Xin\Cache\RedisLock;
use Xin\Contracts\Cache\LockProvider;

class Redis extends RedisDriver implements LockProvider{

	/**
	 * @inheritDoc
	 */
	public function lock($name, $seconds = 0, $owner = null){
		return new RedisLock($this->handler, $this->getCacheKey($name), $seconds, $owner);
	}

	/**
	 * @inheritDoc
	 */
	public function restoreLock($name, $owner){
		return $this->lock($name, 0, $owner);
	}
}
