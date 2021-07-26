<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications\Events;

class NotificationSending{

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
	 * Create a new event instance.
	 *
	 * @param mixed                                  $notifiable
	 * @param \Illuminate\Notifications\Notification $notification
	 * @param string                                 $channel
	 * @return void
	 */
	public function __construct($notifiable, $notification, $channel){
		$this->channel = $channel;
		$this->notifiable = $notifiable;
		$this->notification = $notification;
	}
}
