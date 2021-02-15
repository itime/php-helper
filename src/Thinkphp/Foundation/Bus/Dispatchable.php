<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Bus;

use think\facade\Queue;

trait Dispatchable{
	
	//	/**
	//	 * 默认队列
	//	 *
	//	 * @var string
	//	 */
	//	protected static $defaultQueue = '';
	
	/**
	 * Dispatch the job with the given arguments.
	 *
	 * @param int    $delay
	 * @param mixed  $data
	 * @param string $queue
	 * @return mixed
	 */
	public static function dispatch($delay, $data = null, $queue = ''){
		$queue = static::resolveQueue($queue);
		return Queue::later($delay, static::class, $data, $queue);
	}
	
	/**
	 * Dispatch a command to its appropriate handler in the current process.
	 *
	 * @param mixed  $data
	 * @param string $queue
	 * @return mixed
	 */
	public static function dispatchNow($data = null, $queue = ''){
		$queue = static::resolveQueue($queue);
		return Queue::push(static::class, $data, $queue);
	}
	
	/**
	 * 解析队列名称
	 *
	 * @param string $queue
	 * @return string
	 */
	private static function resolveQueue($queue){
		if(empty($queue) && property_exists(static::class, 'defaultQueue')){
			$queue = static::$defaultQueue;
		}
		
		return $queue;
	}
	
}
