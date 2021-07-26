<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Mail;

interface MailQueue{

	/**
	 * Queue a new e-mail message for sending.
	 *
	 * @param string|array|\Illuminate\Contracts\Mail\Mailable $view
	 * @param string|null                                      $queue
	 * @return mixed
	 */
	public function queue($view, $queue = null);

	/**
	 * Queue a new e-mail message for sending after (n) seconds.
	 *
	 * @param \DateTimeInterface|\DateInterval|int             $delay
	 * @param string|array|\Illuminate\Contracts\Mail\Mailable $view
	 * @param string|null                                      $queue
	 * @return mixed
	 */
	public function later($delay, $view, $queue = null);
}
