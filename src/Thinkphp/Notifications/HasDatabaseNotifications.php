<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications;

trait HasDatabaseNotifications{

	/**
	 * Get the entity's notifications.
	 *
	 * @return \think\model\relation\MorphMany
	 */
	public function notifications(){
		return $this->morphMany(DatabaseNotification::class, 'notifiable')->order('created_at', 'desc');
	}

	/**
	 * Get the entity's read notifications.
	 *
	 * @return \think\model\relation\MorphMany
	 */
	public function readNotifications(){
		return $this->notifications()->whereNotNull('read_at');
	}

	/**
	 * Get the entity's unread notifications.
	 *
	 * @return \think\model\relation\MorphMany
	 */
	public function unreadNotifications(){
		return $this->notifications()->whereNull('read_at');
	}
}
