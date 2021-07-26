<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications;

use think\model\Collection;

class DatabaseNotificationCollection extends Collection{

	/**
	 * Mark all notifications as read.
	 *
	 * @return void
	 */
	public function markAsRead(){
		$this->each(function($notification){
			$notification->markAsRead();
		});
	}

	/**
	 * Mark all notifications as unread.
	 *
	 * @return void
	 */
	public function markAsUnread(){
		$this->each(function($notification){
			$notification->markAsUnread();
		});
	}
}
