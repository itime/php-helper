<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications;

class Notification{

	/**
	 * The unique identifier for the notification.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The locale to be used when sending the notification.
	 *
	 * @var string|null
	 */
	public $locale;

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn(){
		return [];
	}

	/**
	 * Set the locale to send this notification in.
	 *
	 * @param string $locale
	 * @return $this
	 */
	public function locale($locale){
		$this->locale = $locale;

		return $this;
	}
}
