<?php

namespace Xin\Thinkphp\Foundation;

use think\facade\Cache;

class QueueUtil
{
	/**
	 * 重启队列
	 * @return void
	 */
	public static function restart()
	{
		Cache::set('think:queue:restart', now()->getTimestamp());
	}
}