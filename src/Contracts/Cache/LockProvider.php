<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Cache;

interface LockProvider{
	
	/**
	 * Get a lock instance.
	 *
	 * @param string      $name
	 * @param int         $seconds
	 * @param string|null $owner
	 * @return \Xin\Contracts\Cache\Lock
	 */
	public function lock($name, $seconds = 0, $owner = null);
	
	/**
	 * Restore a lock instance using the owner identifier.
	 *
	 * @param string $name
	 * @param string $owner
	 * @return \Xin\Contracts\Cache\Lock
	 */
	public function restoreLock($name, $owner);
}
