<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Notifications\Events;

class NotificationFailed{

	use Queueable, SerializesModels;

	/**
	 * The notifiable entity who received the notification.
	 *
	 * @var mixed
	 */
	public $notifiable;

	/**
	 * The notification instance.
	 *
	 * @var \Illuminate\Notifications\Notification
	 */
	public $notification;

	/**
	 * The channel name.
	 *
	 * @var string
	 */
	public $channel;

	/**
	 * The data needed to process this failure.
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * Create a new event instance.
	 *
	 * @param mixed                                  $notifiable
	 * @param \Illuminate\Notifications\Notification $notification
	 * @param string                                 $channel
	 * @param array                                  $data
	 * @return void
	 */
	public function __construct($notifiable, $notification, $channel, $data = []){
		$this->data = $data;
		$this->channel = $channel;
		$this->notifiable = $notifiable;
		$this->notification = $notification;
	}
}
