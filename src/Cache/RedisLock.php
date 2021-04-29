<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Cache;

class RedisLock extends AbstractLock{

	/**
	 * The Redis factory implementation.
	 *
	 * @var \Redis
	 */
	protected $redis;

	/**
	 * Create a new lock instance.
	 *
	 * @param \Redis      $redis
	 * @param string      $name
	 * @param int         $seconds
	 * @param string|null $owner
	 * @return void
	 */
	public function __construct($redis, $name, $seconds, $owner = null){
		parent::__construct($name, $seconds, $owner);

		$this->redis = $redis;
	}

	/**
	 * Attempt to acquire the lock.
	 *
	 * @return bool
	 */
	public function acquire(){
		$result = $this->redis->setnx($this->name, $this->owner);

		if($result != 0 && $this->seconds > 0){
			$this->redis->expire($this->name, $this->seconds);
		}

		return $result != 0;
	}

	/**
	 * Release the lock.
	 *
	 * @return void
	 */
	public function release(){
		$this->redis->eval(LuaScripts::releaseLock(), [$this->name, $this->owner], 1);
	}

	/**
	 * Releases this lock in disregard of ownership.
	 *
	 * @return void
	 */
	public function forceRelease(){
		$this->redis->del($this->name);
	}

	/**
	 * Returns the owner value written into the driver for this lock.
	 *
	 * @return string
	 */
	protected function getCurrentOwner(){
		return $this->redis->get($this->name);
	}
}
